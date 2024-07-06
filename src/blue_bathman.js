const { send_message } = require('tg_api');


async function on_request(token, body) {
  try {
    const response = await send_message(token, body.message.chat.id, body.message.text);
    return {
        statusCode: 200,
        body: JSON.stringify(response),
    };
  }
  catch (error) {
    return {
        status: 500,
        body: JSON.stringify({ error: 'Failed to send message' }),
    };
  }
}

module.exports = { on_request };
