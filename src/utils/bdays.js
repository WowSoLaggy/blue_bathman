const moment = require('moment');
const ydb_utils = require('./ydb_utils');

const { translate_month_to_russian } = require('./translation');
const { get_user_subs } = require('./users');


let _cache = null;


async function get_bdays() {
  if (!_cache) {
    query = `SELECT * FROM \`bdays/bdays_tbl\``;

    try {
      const ydb = await ydb_utils.get();
      const result = await ydb.query(query);
      result.forEach(row => console.log(row));
      _cache = result;
    } catch (error) {
      console.error('Error fetching users:', error);
      throw error;
    }
  }

  return _cache;
}


async function get_bdays_from_groups(group_ids){
  const bdays = await get_bdays();
  return bdays.filter(bday => group_ids.includes(bday['group']));
}


async function get_bdays_12(user_id) {
  const subs = await get_user_subs(user_id);
  bdays = await get_bdays_from_groups(subs);

  // Sort bdays ignoring year
  bdays.sort((a, b) => {
    const a_date = moment(a['date']).set('year', 2000);
    const b_date = moment(b['date']).set('year', 2000);
    return a_date.diff(b_date);
  });
  
  // Fill current age for each bday
  const now = moment();
  const get_age = (birthday, date) => {
    return moment(date).diff(moment(birthday), 'years');
  };
  bdays.forEach(bday => {
    bday['age'] = get_age(bday['date'], now);
    // Increment age by one if bday is not today and is not in this month
    const is_this_month = moment(bday['date']).set('year', now.year()).isSame(now, 'month');
    const is_today = moment(bday['date']).set('year', now.year()).isSame(now, 'day');
    const after_today = moment(bday['date']).set('year', now.year()).isAfter(now, 'day');
    if (after_today || (!is_this_month && !is_today))
      bday['age']++;
  });

  // Iterate over bdays, move all bdays before start of current month (ignoring year) to the end (but keep it if it is today)
  let i = 0;
  while (i < bdays.length && moment(bdays[i]['date']).set('year', now.year()).isBefore(now, 'month')) {
    i++;
  }
  bdays = bdays.concat(bdays.splice(0, i));

  return bdays;
}

async function get_bdays_2(user_id) {
  // Call get_bdays_12 and return bdays this month and the next one
  const bdays = await get_bdays_12(user_id);
  const now = moment();
  const month = now.month();
  const next_month = (month + 1) % 12;
  return bdays.filter(bday => {
    const bday_month = moment(bday['date']).month();
    return bday_month === month || bday_month === next_month;
  });
}


async function get_bdays_tomorrow(user_id) {
  const bdays = await get_bdays_12(user_id);
  const tomorrow = moment().add(1, 'days');
  return bdays.filter(bday => moment(bday['date']).set('year', tomorrow.year()).isSame(tomorrow, 'day'));
}

async function get_bdays_after_tommorow(user_id) {
  const bdays = await get_bdays_12(user_id);
  const after_tomorrow = moment().add(2, 'days');
  return bdays.filter(bday => moment(bday['date']).set('year', after_tomorrow.year()).isSame(after_tomorrow, 'day'));
}


async function format_bdays_per_month(bdays) {
  let formatted_bdays = '';
  let currentMonth = '';
  bdays.forEach(bday => {
    const month = moment(bday['date']).format('MMMM');
    const month_ru = translate_month_to_russian(month);

    if (month !== currentMonth) {
      formatted_bdays += `\n${month_ru}\n-----------------------------------\n`;
      currentMonth = month;
    }
    const day = moment(bday['date']).format('D');
    const name = bday['name'];
    const age = bday['age'];
    formatted_bdays += `${day} ${month_ru} - ${name} (${age} yo.)\n`;
  });

  // Remove the first newline
  formatted_bdays = formatted_bdays.slice(1);

  return formatted_bdays;
}

async function format_bdays_on_day(bdays, date) {
  if (!bdays.length)
    return '';
  let formatted_bdays = '';
  const day = moment(date).format('D');
  const month = moment(date).format('MMMM');
  const month_ru = translate_month_to_russian(month);
  formatted_bdays += `${day} ${month_ru} наступает ДР у:\n`;
  bdays.forEach(bday => {
    const name = bday['name'];
    const age = bday['age'];
    formatted_bdays += `${name} (${age} yo.)\n`;
  });
  return formatted_bdays;
}


async function get_bdays_12_formatted(user_id) {
  const bdays = await get_bdays_12(user_id);
  return await format_bdays_per_month(bdays);
}

async function get_bdays_2_formatted(user_id) {
  const bdays = await get_bdays_2(user_id);
  return await format_bdays_per_month(bdays);
}

async function get_bdays_tomorrow_formatted(user_id) {
  const bdays = await get_bdays_tomorrow(user_id);
  return await format_bdays_on_day(bdays, moment().add(1, 'days'));
}

async function get_bdays_after_tomorrow_formatted(user_id) {
  const bdays = await get_bdays_after_tommorow(user_id);
  return await format_bdays_on_day(bdays, moment().add(2, 'days'));
}


module.exports = { get_bdays_12_formatted, get_bdays_2_formatted, get_bdays_tomorrow_formatted, get_bdays_after_tomorrow_formatted };
