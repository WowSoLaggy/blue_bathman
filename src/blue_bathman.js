const { send_message } = require('tg_api');


async function on_request(token, body) {
  try {
    const response = await send_message(token, body.message.chat.id, body.message.text);
    return {
        status: 200,
        body: response
    };
  }
  catch (error) {
    return {
        status: 500,
        body: error
    };
  }
}

module.exports = { on_request };
