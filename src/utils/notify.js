const bot = require('./bot');

const { get_users_to_notify } = require('./users.js');
const { get_bdays_tomorrow_formatted } = require('./bdays.js');


async function notify_users() {
  const users_to_notify = await get_users_to_notify();

  for (const user of users_to_notify) {
    const notification_text = await get_bdays_tomorrow_formatted(user.user_id);
    if (notification_text)
      await bot.sendMessage(user.user_id, notification_text);
  }
}


module.exports = { notify_users };
