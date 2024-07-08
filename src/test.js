const dotenv = require('dotenv');

const { on_request, on_notify } = require('./blue_bathman.js');


async function run_test() {

  dotenv.config();
  const TELEGRAM_TOKEN = process.env.TELEGRAM_TOKEN;
  const CHAT_ID = process.env.CHAT_ID;

  on_notify(TELEGRAM_TOKEN);
  return;

  const body = {
    update_id: 822997335,
    message: {
      message_id: 845,
      from: {
        id: 305099932,
        is_bot: false,
        first_name: 'Anton',
        last_name: 'Egorov',
        username: 'WowSoLaggy',
        language_code: 'en'
      },
      chat: {
        id: 305099932,
        first_name: 'Anton',
        last_name: 'Egorov',
        username: 'WowSoLaggy',
        type: 'private'
      },
      date: 1720208436,
      text: '/help'
    }
  };
  const response = await on_request(TELEGRAM_TOKEN, body);
  console.log(response);
}

run_test();
