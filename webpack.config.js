// WordPress webpack config.
const defaultConfig = require('@wordpress/scripts/config/webpack.config');

// Plugins.
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

// Utilities.
const path = require('path');

// Add any new entry points by extending the webpack config.
module.exports = {
    ...defaultConfig,
    entry: {
      'js/auth': path.resolve(process.cwd(), 'assets/js/auth.js'),
      'js/commission': path.resolve(process.cwd(), 'assets/js/commission.js'),
      'js/contract': path.resolve(process.cwd(), 'assets/js/contract.js'),
      'js/deposit': path.resolve(process.cwd(), 'assets/js/deposit.js'),
      'js/dispute': path.resolve(process.cwd(), 'assets/js/dispute.js'),
      'js/find-opportunities': path.resolve(process.cwd(), 'assets/js/find-opportunities.js'),
      'js/find-commercial-agents': path.resolve(process.cwd(), 'assets/js/find-commercial-agents.js'),
      'js/main': path.resolve(process.cwd(), 'assets/js/main.js'),
      'js/opportunity': path.resolve(process.cwd(), 'assets/js/opportunity.js'),
      'js/payment': path.resolve(process.cwd(), 'assets/js/payment.js'),
      'js/profile': path.resolve(process.cwd(), 'assets/js/profile.js'),
      'css/main': path.resolve(process.cwd(), 'assets/css/main.css'),
      'css/auth': path.resolve(process.cwd(), 'assets/css/auth.css'),
      'css/admin-dashboard': path.resolve(process.cwd(), 'assets/css/admin-dashboard.css'),
      'css/header': path.resolve(process.cwd(), 'assets/css/header.css'),
      'js/home': path.resolve(process.cwd(), 'assets/js/home.js'),
      'css/home': path.resolve(process.cwd(), 'assets/css/home.css'),
      'js/frontend': path.resolve(process.cwd(), 'assets/js/frontend.js'),
      'css/frontend': path.resolve(process.cwd(), 'assets/css/frontend.css'),
      'css/blog': path.resolve(process.cwd(), 'assets/css/blog.css'),
    },
    output: {
        path: path.resolve(process.cwd(), 'dist'),
        filename: '[name].js',
        publicPath: '/dist/',
    },
    module: {
        rules: [
            ...defaultConfig.module.rules,
            {
                test: /\.scss$/,
                use: [
                    'style-loader', // Creates `style` nodes from JS strings
                    'css-loader', // Translates CSS into CommonJS
                    'postcss-loader', // Process CSS with PostCSS
                    'sass-loader', // Compiles Sass to CSS
                ],
                include: path.resolve(process.cwd(), 'resources/scss'),
            },
        ],
    },
    plugins: [
        ...defaultConfig.plugins,
        new RemoveEmptyScriptsPlugin({
            stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(process.cwd(), 'resources/js'),
        },
        extensions: ['.js', '.jsx', '.scss'],
    },
};
