// server.cjs

const express = require('express');
const http = require('http');
const {v4 : uuidv4} = require('uuid');

const app = express(); // Membuat instansi Express
// const server = http.createServer(app);
const port = process.env.PORT || 8000;
let mysql = require('mysql');
const { randomUUID } = require('crypto');

let connection = mysql.createConnection({
    host: '192.168.6.85',
    user: 'dev',
    password: 'd3v3D1indonesia!',
    database: 'Project_manage',
    port : 3306,
});

const server = app.listen(`${port}`, () => {
    console.log(`Server listening on ${port}`);
    connection.connect();
});

app.get('/', function(req, res){
    res.send('<p>node js v18.14.0</p>');
});

const io = require('socket.io')(server,{
    cors: {origin: "*"}
});

io.on('connection',(socket)=>{
    console.log('Client connected');

    // Start socket on tasklist
    socket.on('tasklist', (data) => {
        // Fetch the employee's name based on the karyawan_id from m_employee table
        connection.query('SELECT name FROM m_employee WHERE id = ?', data.karyawan_id, (error, employeeResult) => {
            if (error) throw error;

        // Fetch the pc_id based on the project_id from m_project table
        connection.query('SELECT pc_id FROM m_project WHERE id = ?', data.project_id, (error, projectResult) => {
            if (error) throw error;

                if (employeeResult.length > 0 && projectResult.length > 0) {
                    const employeeName = employeeResult[0].name;
                    const pcid = projectResult[0].pc_id;

                    let notifikasi = {
                        transactionnumber: data.transactionnumber,
                        karyawan_id: data.karyawan_id,
                        notif: `progress 100%  from ${employeeName}`, // Include the employee's name in the notification
                        created_at: new Date(),
                        created_by: data.karyawan_id,
                        sender: data.karyawan_id,
                        receiver: pcid,
                    };

                    connection.query('INSERT INTO notifikasi SET ?', notifikasi, (error, results) => {
                        if (error) throw error;

                        // successful
                        // console.log(results);

                        // socket.io send to client selain yang mengirim
                        socket.broadcast.emit('tasklist', notifikasi);
                    });
                }
            });
        });
    });
    // END socket on tasklist


    socket.on('disconnect',()=>{
        console.log('Client disconnected');
    })
})
