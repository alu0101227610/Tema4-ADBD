DROP DATABASE IF EXISTS mydb;
CREATE DATABASE mydb;

DROP TABLE IF EXISTS mydb.Cliente CASCADE;
CREATE TABLE IF NOT EXISTS mydb.Cliente (
  dni VARCHAR(45) NOT NULL,
  borrado BOOLEAN NOT NULL,
  nombre VARCHAR(45) NULL,
  apellidos VARCHAR(45) NULL,
  email VARCHAR(45) NULL,
  telefono VARCHAR(45) NULL,
  codigo_postal VARCHAR(45) NULL,
  direccion_postal VARCHAR(90) NULL,
  PRIMARY KEY (dni))
;

DROP TABLE IF EXISTS mydb.Producto CASCADE;
CREATE TABLE IF NOT EXISTS mydb.Producto (
  codigo VARCHAR(45) NOT NULL,
  borrado BOOLEAN NOT NULL,
  nombre VARCHAR(45) NULL,
  familia VARCHAR(45) NULL,
  descripcion VARCHAR(180) NULL,
  stock INT(20) NULL,
  dimensionX INT(10) NULL,
  dimensionY INT(10) NULL,
  dimensionZ INT(10) NULL,
  peso INT(10) NULL,
  PVP DECIMAL(10,2) NULL,
  PRIMARY KEY (Codigo))
;

DROP TABLE IF EXISTS mydb.Compra CASCADE;
CREATE TABLE IF NOT EXISTS mydb.Compra (
  codigo INT NOT NULL AUTO_INCREMENT,
  borrado BOOLEAN NOT NULL,
  cliente_dni VARCHAR(45) NOT NULL,
  producto_codigo VARCHAR(45) NOT NULL,
  fecha timestamp NOT NULL,
  cantidad INT(20) NOT NULL,
  PRIMARY KEY (codigo),
  CONSTRAINT fk_Cliente_compra
    FOREIGN KEY (cliente_dni)
    REFERENCES mydb.Cliente(dni)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_Producto_compra
    FOREIGN KEY (producto_codigo)
    REFERENCES mydb.Producto(codigo)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
;

START TRANSACTION;
INSERT INTO mydb.Cliente (dni, borrado, nombre, apellidos, email, telefono, codigo_postal, direccion_postal) VALUES ('11245677L', false, 'cliente1', 'apellidosCliente1', 'cliente1@email.es', '131213342', '38435', 'c/calleCliente1, ciudadCliente1');
INSERT INTO mydb.Cliente (dni, borrado, nombre, apellidos, email, telefono, codigo_postal, direccion_postal) VALUES ('21345478L', false, 'cliente2', 'apellidosCliente2', 'cliente2@email.es', '261223346', '38430', 'c/calleCliente2, ciudadCliente2');
INSERT INTO mydb.Cliente (dni, borrado, nombre, apellidos, email, telefono, codigo_postal, direccion_postal) VALUES ('31355679L', false, 'cliente3', 'apellidosCliente3', 'cliente3@email.es', '371243349', '38584', 'c/calleCliente3, ciudadCliente3');
COMMIT;

START TRANSACTION;
INSERT INTO mydb.Producto (codigo, borrado, nombre, familia, descripcion, stock, dimensionX, dimensionY, dimensionZ, peso, PVP) VALUES ('1234ABCD', false, 'producto1', 'familia1', 'descripcion del producto 1', 3, 100, 40, 80, 2200, 1000.97);
INSERT INTO mydb.Producto (codigo, borrado, nombre, familia, descripcion, stock, dimensionX, dimensionY, dimensionZ, peso, PVP) VALUES ('2434ABCG', false, 'producto2', 'familia2', 'descripcion del producto 2', 37, 180, 20, 20, 600, 20.51);
INSERT INTO mydb.Producto (codigo, borrado, nombre, familia, descripcion, stock, dimensionX, dimensionY, dimensionZ, peso, PVP) VALUES ('3734ABCZ', false, 'producto3', 'familia1', 'descripcion del producto 3', 11, 50, 40, 10, 100, 1.01);
COMMIT;

START TRANSACTION;
INSERT INTO mydb.Compra (borrado, cliente_dni, producto_codigo, fecha, cantidad) VALUES (false, '11245677L', '1234ABCD', timestamp('2021-05-10'), 1);
INSERT INTO mydb.Compra (borrado, cliente_dni, producto_codigo, fecha, cantidad) VALUES (false, '31355679L', '2434ABCG', timestamp('2021-05-10'), 1);
INSERT INTO mydb.Compra (borrado, cliente_dni, producto_codigo, fecha, cantidad) VALUES (false, '21345478L', '3734ABCZ', timestamp('2021-10-21'), 4);
COMMIT;

