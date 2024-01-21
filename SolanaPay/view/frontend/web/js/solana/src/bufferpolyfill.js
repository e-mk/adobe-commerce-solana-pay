// Polyfill for Buffer

global.Buffer = global.Buffer || require('buffer').Buffer;

