# WP AI Chatbot

A WordPress chatbot plugin powered by Google Gemini AI. This plugin adds a customizable chat widget to your WordPress site that allows visitors to interact with an AI assistant.

## Features

- AI-powered chat interface using Google Gemini
- Customizable chat widget appearance
- Automatic updates through GitHub releases
- Easy configuration through WordPress admin panel

## Installation

### From GitHub

1. Download the latest release from the [releases page](https://github.com/YOUR-USERNAME/wp-chatbot/releases)
2. Upload the plugin through WordPress admin panel:
   - Go to Plugins > Add New > Upload Plugin
   - Choose the downloaded zip file
   - Click "Install Now"
   - Activate the plugin

### Manual Installation

1. Clone this repository
2. Copy the `wp-chatbot` folder to your WordPress plugins directory (`wp-content/plugins/`)
3. Activate the plugin through the WordPress admin panel

## Configuration

1. Go to WordPress admin panel
2. Navigate to "AI Chatbot" in the sidebar
3. Configure the following settings:
   - Enter your Google Gemini API key
   - Customize the chat window title
   - Choose primary color theme
   - Upload custom chat button image (optional)

## Development

### Setting Up GitHub Integration

1. Create a new GitHub repository
2. Update the following in `wp-chatbot.php`:
   - Plugin URI: Your GitHub repository URL
   - Author URI: Your GitHub profile URL
   - GitHub Plugin URI: YOUR-USERNAME/wp-chatbot
   - Primary Branch: main (or your default branch)

### Creating Releases

1. Make your code changes
2. Update the version number in `wp-chatbot.php`
3. Create a new release on GitHub:
   - Go to your repository
   - Click "Releases" > "Create a new release"
   - Tag version should match plugin version (e.g., "1.0.1")
   - Include changelog in release description
   - Publish release

### Automatic Updates

The plugin includes an updater that checks GitHub releases for new versions. When you publish a new release:

1. WordPress sites using the plugin will be notified of the update
2. Users can update directly through their WordPress admin panel
3. The update process preserves user settings

## Contributing

1. Fork the repository
2. Create a new branch for your feature
3. Submit a pull request

## License

This project is licensed under the GPL v2 or later.

## Credits

- Uses Google Gemini AI for natural language processing
- Built for WordPress
