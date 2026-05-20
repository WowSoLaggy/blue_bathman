const dotenv = require('dotenv');
dotenv.config();

const { on_request, on_notify } = require('./blue_bathman.js');
const { user_exists } = require('./utils/users.js');


async function run_test() {


  if (!process.env.TELEGRAM_TOKEN) {
    console.error('TELEGRAM_TOKEN is not set in .env file');
    return;
  }
  if (!process.env.YDB_ENDPOINT) {
    console.error('YDB_ENDPOINT is not set in .env file');
    return;
  }
  if (!process.env.YDB_DATABASE_PATH) {
    console.error('YDB_DATABASE_PATH is not set in .env file');
    return;
  }
  if (!process.env.ACCESS_KEY_PATH) {
    console.error('ACCESS_KEY_PATH is not set in .env file');
    return;
  }
  
  console.log('TELEGRAM_TOKEN:', process.env.TELEGRAM_TOKEN);
  console.log('YDB_ENDPOINT:', process.env.YDB_ENDPOINT);
  console.log('YDB_DATABASE_PATH:', process.env.YDB_DATABASE_PATH);
  console.log('ACCESS_KEY_PATH:', process.env.ACCESS_KEY_PATH);

  // const exists_test = await user_exists(5236221588);
  // console.log('User exists test:', exists_test);

  // on_notify();
  // return;

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
      text: '/all'
    }
  };
  const response = await on_request(body);
  console.log('Response from on_request:');
  console.log(response);
}

run_test();
