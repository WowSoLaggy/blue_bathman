const csv = require('csv-parser');


async function user_exists(user_id) {
  // Read from CSV file tables/users.csv, whether user with such user_id exists
  const users_csv_file = 'tables/users.csv';
  const users = [];
  fs.createReadStream(users_csv_file)
    .pipe(csv())
    .on('data', (row) => {
      users.push(row);
    })
    .on('end', () => {
      console.log(users);
    });

  // Return true if user exists, false otherwise
  return users.find(user => user.id === user_id);
}
