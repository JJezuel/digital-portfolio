// server.js
const express = require('express');
const path = require('path');

const app = express();
const PORT = 3000;

// Serve all static files (HTML, CSS, JS, images) from this folder
app.use(express.static(__dirname));

// Explicitly send index.html on root route
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'index.html'));
});

app.listen(PORT, () => {
  console.log(`Server running at http://localhost:${PORT}`);
});