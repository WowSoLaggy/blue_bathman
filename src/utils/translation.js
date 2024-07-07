function translate_month_to_russian(month) {
  switch (month) {
    case 'January': return 'Янв';
    case 'February': return 'Фев';
    case 'March': return 'Мар';
    case 'April': return 'Апр';
    case 'May': return 'Май';
    case 'June': return 'Июн';
    case 'July': return 'Июл';
    case 'August': return 'Авг';
    case 'September': return 'Сен';
    case 'October': return 'Окт';
    case 'November': return 'Ноя';
    case 'December': return 'Дек';
  }
}

module.exports = { translate_month_to_russian };
