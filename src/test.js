const dotenv = require('dotenv');

const { on_request, on_notify } = require('./blue_bathman.js');


async function run_test() {

  dotenv.config();

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
  if (!process.env.YDB_PRIVATE_KEY) {
    console.error('YDB_PRIVATE_KEY is not set in .env file');
    return;
  }
  if (!process.env.YDB_ID) {
    console.error('YDB_ID is not set in .env file');
    return;
  }
  if (!process.env.YDB_SERVICE_ACCOUNT_ID) {
    console.error('YDB_SERVICE_ACCOUNT_ID is not set in .env file');
    return;
  }
  
  console.log('TELEGRAM_TOKEN:', process.env.TELEGRAM_TOKEN);
  console.log('YDB_ENDPOINT:', process.env.YDB_ENDPOINT);
  console.log('YDB_DATABASE_PATH:', process.env.YDB_DATABASE_PATH);
  console.log('YDB_PRIVATE_KEY:', process.env.YDB_PRIVATE_KEY);
  console.log('YDB_ID:', process.env.YDB_ID);
  console.log('YDB_SERVICE_ACCOUNT_ID:', process.env.YDB_SERVICE_ACCOUNT_ID);

  //on_notify(TELEGRAM_TOKEN);
  //return;

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
  const response = await on_request(process.env.TELEGRAM_TOKEN, body);
  console.log('Response from on_request:');
  console.log(response);
}

run_test();
