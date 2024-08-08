const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const path = require('path');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

module.exports = {
    ...defaultConfig,
    mode: 'production',
    entry: {
        'js/auth': path.resolve(process.cwd(), 'assets/js/auth.js'),
        'js/commission': path.resolve(process.cwd(), 'assets/js/commission.js'),
        'js/contract': path.resolve(process.cwd(), 'assets/js/contract.js'),
        'js/deposit': path.resolve(process.cwd(), 'assets/js/deposit.js'),
        'js/dispute': path.resolve(process.cwd(), 'assets/js/dispute.js'),
        'js/find-opportunities': path.resolve(process.cwd(), 'assets/js/find-opportunities.js'),
        'js/main': path.resolve(process.cwd(), 'assets/js/main.js'),
        'js/opportunity': path.resolve(process.cwd(), 'assets/js/opportunity.js'),
        'js/payment': path.resolve(process.cwd(), 'assets/js/payment.js'),
        'js/profile': path.resolve(process.cwd(), 'assets/js/profile.js'),
        'css/auth': path.resolve(process.cwd(), 'assets/css/auth.scss'),
        'css/admin-dashboard': path.resolve(process.cwd(), 'assets/css/admin-dashboard.scss'),
        'css/header': path.resolve(process.cwd(), 'assets/css/header.scss'),
        'css/main': path.resolve(process.cwd(), 'assets/css/main.scss'),
    },
    output: {
        path: path.resolve(process.cwd(), 'public/assets'),
        filename: '[name].js',
        publicPath: '/assets/',
    },
    module: {
        rules: [
            ...defaultConfig.module.rules,
            {
                test: /\.scss$/,
                use: [
                    'style-loader',
                    'css-loader',
                    'postcss-loader',
                    'sass-loader',
                ],
                include: path.resolve(process.cwd(), 'assets/scss'),
            },
        ],
    },
    plugins: [
        new CleanWebpackPlugin(),
        new RemoveEmptyScriptsPlugin({
            stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
        }),
        // Otros plugins si es necesario
    ],
    resolve: {
        alias: {
            '@': path.resolve(process.cwd(), 'assets/js'),
        },
        extensions: ['.js', '.jsx', '.scss'],
    },
    optimization: {
        minimize: true,
        minimizer: [
            '...',
            new CssMinimizerPlugin(),
        ],
    },
};
