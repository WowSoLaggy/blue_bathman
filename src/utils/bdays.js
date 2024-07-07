const csv = require('csv-parser');
const fs = require('fs');
const moment = require('moment');
const path = require('path');

const { translate_month_to_russian } = require('./translation');
const { get_user_subs } = require('./users');


let _cache = null;
async function read_bdays_file() {
  if (!_cache) {
    return new Promise((resolve, reject) => {
      const results = [];
      const csvFilePath = path.resolve(__dirname, './../../tables/bdays.csv');
      fs.createReadStream(csvFilePath)
        .pipe(csv())
        .on('data', (data) => results.push(data))
        .on('end', () => {
          results.forEach(record => {
            record['group_id'] = parseInt(record['group_id']);
            record['date'] = new Date(record['date']);
          });
          _cache = results;
          resolve(results);
        })
        .on('error', (err) => reject(err));
    });
  }
  return _cache;
}


async function get_bdays_from_groups(group_ids){
  const bdays = await read_bdays_file();
  return bdays.filter(bday => group_ids.includes(bday['group_id']));
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
    const is_same = moment(bday['date']).set('year', now.year()).isSame(now, 'day');
    if (!is_same)
      bday['age']++;
  });

  // Iterate over bdays, move all bdays before today (ignoring year) to the end (but keep it if it is today)
  let i = 0;
  while (i < bdays.length && moment(bdays[i]['date']).set('year', now.year()).isBefore(now, 'day')) {
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


async function format_bdays(bdays) {
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


async function get_bdays_12_formatted(user_id) {
  const bdays = await get_bdays_12(user_id);
  return await format_bdays(bdays);
}

async function get_bdays_2_formatted(user_id) {
  const bdays = await get_bdays_2(user_id);
  return await format_bdays(bdays);
}


module.exports = { get_bdays_12_formatted, get_bdays_2_formatted };
