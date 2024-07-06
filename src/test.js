const dotenv = require('dotenv');

const { on_request } = require('./blue_bathman.js');


dotenv.config();
const TELEGRAM_TOKEN = process.env.TELEGRAM_TOKEN;
const CHAT_ID = process.env.CHAT_ID;


async function run_test() {
  const event = {
    chat: {
      id: CHAT_ID
    },
    text: 'Hello, world!'
  };
  const response = await on_request(TELEGRAM_TOKEN, event);
  console.log(response);
}

run_test();
