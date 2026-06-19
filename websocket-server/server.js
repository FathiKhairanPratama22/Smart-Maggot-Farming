const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');

const app = express();
app.use(cors());
app.use(express.json());

const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"]
    }
});

// Endpoint untuk menerima trigger (emit) dari CI4 Backend
app.post('/emit', (req, res) => {
    const { event, data } = req.body;
    
    if (!event || !data) {
        return res.status(400).json({ error: "Parameter event dan data wajib diisi." });
    }

    // Broadcast ke seluruh client yang terkoneksi
    io.emit(event, data);
    console.log(`[EMIT] Event: ${event} | Data:`, data);

    res.json({ status: "success", message: `Event ${event} broadcasted successfully.` });
});

io.on('connection', (socket) => {
    console.log(`[CLIENT CONNECTED] Socket ID: ${socket.id}`);

    socket.on('disconnect', () => {
        console.log(`[CLIENT DISCONNECTED] Socket ID: ${socket.id}`);
    });
});

const PORT = process.env.PORT || 3000;
server.listen(PORT, () => {
    console.log(`🚀 WebSocket Server berjalan di port ${PORT}`);
    console.log(`Menunggu trigger emit di http://localhost:${PORT}/emit`);
});
