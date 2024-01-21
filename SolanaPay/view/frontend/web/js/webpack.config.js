const path = require('path');
const webpack = require('webpack');

module.exports = {
  mode: 'development',
  devtool: false,
  entry: {
    splToken: './node_modules/@solana/spl-token/lib/cjs/index.js',
    buffer: './solana/src/bufferpolyfill.js',
  },
  output: {
    path: path.resolve(__dirname, 'lib'),
    filename: '[name].js',
    library: {
      name: 'splToken',
      type: 'var',
    },
  },
  resolve: {
    fallback: {
      buffer: require.resolve('buffer/'),
      crypto: require.resolve('crypto-browserify'),
      stream: require.resolve('stream-browserify'),
    },
  },
  plugins: [
    new webpack.ProvidePlugin({
      Buffer: ['buffer', 'Buffer'],
    }),
  ],
};
