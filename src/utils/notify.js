const { send_message } = require('tg_api');

const { get_users_to_notify } = require('./users.js');
const { get_bdays_tomorrow_formatted, get_bdays_after_tomorrow_formatted } = require('./bdays.js');


async function notify_users(token) {
  const users_to_notify = await get_users_to_notify();

  for (const user of users_to_notify) {
    notification_text = '';
    notification_text += await get_bdays_tomorrow_formatted(user.user_id);
    if (notification_text)
      notification_text += '\n';
    notification_text += await get_bdays_after_tomorrow_formatted(user.user_id);

    console.log(notification_text);

    if (notification_text)
      return await send_message(token, user.user_id, notification_text);
  }
}


module.exports = { notify_users };
