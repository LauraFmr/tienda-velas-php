-- Inicialización para tienda_velas
CREATE EXTENSION IF NOT EXISTS pgcrypto;


-- Crear DB si no existe (se ignora si ya estamos en la DB)
-- El contenedor ya crea ${POSTGRES_DB}, así que continuamos.


-- Drop en orden de dependencias
DROP TABLE IF EXISTS productos CASCADE;
DROP TABLE IF EXISTS usuarios_roles CASCADE;
DROP TABLE IF EXISTS roles CASCADE;
DROP TABLE IF EXISTS usuarios CASCADE;
DROP TABLE IF EXISTS categorias CASCADE;
DROP TABLE IF EXISTS fragancias CASCADE;


-- Tablas base
CREATE TABLE roles (
	id SERIAL PRIMARY KEY,
	nombre VARCHAR(150) NOT NULL UNIQUE,
	descripcion VARCHAR(255)
);

CREATE TABLE usuarios (
	id SERIAL PRIMARY KEY,
	nombre VARCHAR(100),
	apellido VARCHAR(100),
	username VARCHAR(100) UNIQUE NOT NULL,
	email VARCHAR(255) UNIQUE,
	password VARCHAR(255) NOT NULL,
	is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE usuarios_roles (
	usuario_id INT NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
	rol_id INT NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
	PRIMARY KEY (usuario_id, rol_id)
);

CREATE TABLE productos (
	id SERIAL PRIMARY KEY,
	uuid UUID UNIQUE NOT NULL DEFAULT gen_random_uuid(),
	nombre VARCHAR(200) NOT NULL,
	descripcion TEXT,
	precio NUMERIC(10,2) NOT NULL DEFAULT 0,
	stock INT NOT NULL DEFAULT 0,
	imagen TEXT DEFAULT NULL,
	categoria_id INT REFERENCES categorias(id) ON UPDATE CASCADE ON DELETE SET NULL,
	fragancia_id INT REFERENCES fragancias(id) ON UPDATE CASCADE ON DELETE SET NULL,
	is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);


-- Semillas (roles)
INSERT INTO roles (nombre, descripcion) VALUES
('ADMIN','Administración total'),
('USER','Cliente/usuario');


-- Semillas (usuarios)
INSERT INTO usuarios (nombre, apellido, username, email, password)
VALUES
('Arwen','Undómiel','admin','admin@velas.me',
'$2y$10$9u2gPp4kZ1t0x1g2m3bT0Oq6b1N8f0n8vT7l1g2m3bT0Oq6b1N8f'), -- Admin1234! // Updated hash y new pass Admin123456
('Frodo','Baggins','usuario','frodo@velas.me',
'$2y$10$8Yp9o7mB4N0gV3Q6e1x9uO1l3Jf8b2i7P5h6K3m0N9d2Q1a7Z5Wy'); -- User1234!


-- Asignación de roles
INSERT INTO usuarios_roles (usuario_id, rol_id)
SELECT u.id, r.id FROM usuarios u CROSS JOIN roles r WHERE u.username='admin' AND r.nombre='ADMIN';
INSERT INTO usuarios_roles (usuario_id, rol_id)
SELECT u.id, r.id FROM usuarios u CROSS JOIN roles r WHERE u.username='admin' AND r.nombre='USER';
INSERT INTO usuarios_roles (usuario_id, rol_id)
SELECT u.id, r.id FROM usuarios u CROSS JOIN roles r WHERE u.username='usuario' AND r.nombre='USER';


-- Categorías (temática Tierra Media)
INSERT INTO categorias (nombre, descripcion) VALUES
('Velas de Soja', 'Velas artesanales de cera de soja'),
('Wax Melts', 'Tabletas aromáticas para quemadores'),
('Accesorios', 'Apagavelas, portavelas, mecheros élficos');


-- Fragancias (ejemplo)
INSERT INTO fragancias (nombre, notas) VALUES
('Bosque de Lothlórien', 'Notas verdes, flores de mallorn'),
('Humo de la Comarca', 'Pipa, caramelo, vainilla'),
('Forja de Erebor', 'Ámbar, cuero, resinas');


-- Productos demo
INSERT INTO productos (nombre, descripcion, precio, stock, imagen, categoria_id, fragancia_id)
SELECT 'Vela Lothlórien', 'Vela de soja 200g con notas verdes', 14.90, 20, NULL,
(SELECT id FROM categorias WHERE nombre='Velas de Soja'),
(SELECT id FROM fragancias WHERE nombre='Bosque de Lothlórien');


INSERT INTO productos (nombre, descripcion, precio, stock, imagen, categoria_id, fragancia_id)
SELECT 'Wax Melt Comarca', '6 cubos de 15g', 6.50, 50, NULL,
(SELECT id FROM categorias WHERE nombre='Wax Melts'),
(SELECT id FROM fragancias WHERE nombre='Humo de la Comarca');


INSERT INTO productos (nombre, descripcion, precio, stock, imagen, categoria_id, fragancia_id)
SELECT 'Apagavelas Élfico', 'Acabado plata', 9.90, 15, NULL,
(SELECT id FROM categorias WHERE nombre='Accesorios'), NULL;