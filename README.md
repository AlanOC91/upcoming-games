# PSLegends Upcoming Game Releases

A simple PHP application that fetches and displays upcoming game releases for PlayStation 4 and PlayStation 5 using the IGDB API.

## Features

- Retrieves upcoming game releases for PS4 and PS5 (platforms can be changed).
- Displays games in a table, sorted by release date.
- Uses Bootstrap 5 for styling.
- Utilizes caching to reduce redundant API requests.
- Dark mode theme for better visibility.

## Requirements

- PHP 7.4 or higher.
- Composer for PHP package management.
- IGDB API client credentials.

## Installation

1. Clone this repository:

    ```bash
    git clone https://github.com/AlanOC91/upcoming-game-releases.git
    ```

2. Navigate to the project directory and install dependencies using Composer:

    ```bash
    cd upcoming-game-releases
    composer install
    ```

3. Copy the `.env.example` file to a new file called `.env` and update the `CLIENT_ID` and `CLIENT_SECRET` values with your IGDB API credentials.

4. Run your PHP server and access the application.

## Usage

Once the application is set up, navigate to the homepage to view the list of upcoming games for PS4 and PS5. Should you wish to change platforms, simply pass in an array of IGDB Platform IDs to `fetchUpcomingGames()`.      

## Branding

The application uses PSLegends branding by default. Should you wish to change it please modify the `/assets/` folder.

You should also correctly modify the .env `APP_NAME` abd `APP_URL` values.

## Contributing

Contributions, issues, and feature requests are welcome. Feel free to check [issues page](#) if you want to contribute.

## License

This project is licensed under the MIT License. See the `LICENSE` file for more details.

## Acknowledgements

- [IGDB API](https://www.igdb.com/api)
- [Bootstrap 5](https://getbootstrap.com/)