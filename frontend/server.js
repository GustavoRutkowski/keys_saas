import express from 'express';
import { fileURLToPath } from 'url';
import path, { dirname } from 'path';
import dotenv from 'dotenv';
dotenv.config();

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const app = express();

app.use(express.static(__dirname));

app.get('/', (_, res) => {
    res.sendFile(path.join(__dirname, 'view', 'index.html'));
});

// Alias
app.get('/index', (_, res) => res.redirect('/'));

app.get('/faq', (_, res) => {
    res.sendFile(path.join(__dirname, 'view', 'faq.html'));
});

app.get('/login', (_, res) => {
    res.sendFile(path.join(__dirname, 'view', 'login.html'));
});

app.get('/register', (_, res) => {
    res.sendFile(path.join(__dirname, 'view', 'register.html'));
});

app.get('/dashboard', (_, res) => {
    res.sendFile(path.join(__dirname, 'view', 'dashboard.html'));
});

app.get('/user-settings', (_, res) => {
    res.sendFile(path.join(__dirname, 'view', 'user-settings.html'));
});

app.get('/admin-panel', (_, res) => {
    res.sendFile(path.join(__dirname, 'view', 'admin-panel.html'));
});

app.get('/404', (_, res) => {
    res.status(404).sendFile(path.join(__dirname, 'view', '404.html'));
});

app.get('/418', (_, res) => {
    res.status(418).sendFile(path.join(__dirname, 'view', '418.html'));
});

app.get('/teapot', (_, res) => res.redirect('/418'));
app.use((_, res) => res.redirect('/404'));

const PORT = process.env.SITE_PORT || 5500;

app.listen(PORT, '0.0.0.0', () => console.warn(`Server running in port ${PORT}...`));
