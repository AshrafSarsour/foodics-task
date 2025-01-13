
# Foodics Order Service Testing

This project includes a set of tests for order processing and low stock alert functionality in a Laravel application.

## Prerequisites

Before running the tests, ensure the following are installed on your system:

- PHP (>= 8.0)
- Composer
- MySQL (or your preferred database)
- Node.js and npm (optional, if frontend assets are required)
- Laravel (>= 9.x)

## Setup Instructions

Follow these steps to set up the project for testing:

### 1. Clone the Repository
Clone the repository to your local machine:

```bash
git https://github.com/AshrafSarsour/foodics-task
cd foodics-task
```

### 2. Install Dependencies
Install PHP and Node.js dependencies:

```bash
composer install
```

### 3. Set Up the Environment File
Copy the `.env.example` file to `.env` and configure the necessary environment variables:

```bash
cp .env.example .env
```

Update the database credentials in the `.env` file:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=test_db
DB_USERNAME=root
DB_PASSWORD=secret
```

### 4. Set Up the Database
Run the migrations and seeders to set up the database:

```bash
php artisan migrate --seed
```

If additional test-specific seeding is required:

 
### 5. Run the Tests
Use the following command to run the tests:

```bash
php artisan test
```

This command will execute all the unit tests and display the results in the console.

## Testing Highlights

### Included Tests
- **Order Processing**: Tests the functionality of creating an order and updating ingredient stock.
- **Low Stock Alert**: Ensures a low stock notification is sent when ingredient stock drops below 50%.

### Key Assertions
- Database is updated with the correct stock levels.
- Notifications are triggered for low stock conditions.
- Orders are successfully created and stored.

### Dependencies Used
- `Illuminate\Foundation\Testing\DatabaseTransactions`: Ensures each test rollback all database changes after each test    
- `Illuminate\Support\Facades\Notification`: Mocks notifications to verify they are triggered correctly.

## Additional Notes

- **Reset Database Transaction**: Each test uses the `DatabaseTransactions` trait, so the database transaction is reset after every test.
- **Seed Data**: Make sure essential data is seeded before running tests.
- **Environment-Specific Configurations**: Adjust `.env` variables as needed for local or CI/CD environments.

## Support

If you encounter any issues while setting up or running the tests, feel free to reach out.