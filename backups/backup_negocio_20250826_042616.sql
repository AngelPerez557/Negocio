mysqldump: [Warning] Using a password on the command line interface can be insecure.
-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: negocio
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `articulos`
--

DROP TABLE IF EXISTS `articulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `articulos` (
  `ArticuloID` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `CodigoBarra` varchar(50) DEFAULT NULL,
  `stock` int DEFAULT NULL,
  `PrecioVenta` decimal(10,2) DEFAULT NULL,
  `PrecioCosto` decimal(10,2) DEFAULT NULL,
  `ProveedorID` int DEFAULT NULL,
  `es_gravado` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ArticuloID`),
  KEY `articulos_ibfk_1` (`ProveedorID`),
  CONSTRAINT `articulos_ibfk_1` FOREIGN KEY (`ProveedorID`) REFERENCES `proveedores` (`ProveedorID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articulos`
--

LOCK TABLES `articulos` WRITE;
/*!40000 ALTER TABLE `articulos` DISABLE KEYS */;
INSERT INTO `articulos` VALUES (1,'Mouse óptico','1234567890123',50,15.00,10.00,1,1),(2,'Teclado mecánico','2345678901234',30,45.50,30.00,1,1);
/*!40000 ALTER TABLE `articulos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compradetalle`
--

DROP TABLE IF EXISTS `compradetalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compradetalle` (
  `CompraDetalleID` int NOT NULL,
  `CompraID` int DEFAULT NULL,
  `ArticuloID` int DEFAULT NULL,
  `Cantidad` int DEFAULT NULL,
  `PrecioUnitario` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`CompraDetalleID`),
  KEY `CompraID` (`CompraID`),
  KEY `compradetalle_ibfk_2` (`ArticuloID`),
  CONSTRAINT `compradetalle_ibfk_1` FOREIGN KEY (`CompraID`) REFERENCES `compras` (`CompraID`),
  CONSTRAINT `compradetalle_ibfk_2` FOREIGN KEY (`ArticuloID`) REFERENCES `articulos` (`ArticuloID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compradetalle`
--

LOCK TABLES `compradetalle` WRITE;
/*!40000 ALTER TABLE `compradetalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `compradetalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compras`
--

DROP TABLE IF EXISTS `compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compras` (
  `CompraID` int NOT NULL,
  `ProveedorID` int DEFAULT NULL,
  `FechaCompra` date DEFAULT NULL,
  `TotalCompra` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`CompraID`),
  KEY `compras_ibfk_1` (`ProveedorID`),
  CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`ProveedorID`) REFERENCES `proveedores` (`ProveedorID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compras`
--

LOCK TABLES `compras` WRITE;
/*!40000 ALTER TABLE `compras` DISABLE KEYS */;
/*!40000 ALTER TABLE `compras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facturadetalle`
--

DROP TABLE IF EXISTS `facturadetalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facturadetalle` (
  `FacturaDetalleID` int NOT NULL AUTO_INCREMENT,
  `FacturaID` int DEFAULT NULL,
  `ArticuloID` int DEFAULT NULL,
  `Cantidad` int NOT NULL,
  `PrecioUnitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`FacturaDetalleID`),
  KEY `FacturaID` (`FacturaID`),
  KEY `facturadetalle_ibfk_2` (`ArticuloID`),
  CONSTRAINT `facturadetalle_ibfk_1` FOREIGN KEY (`FacturaID`) REFERENCES `facturas` (`FacturaID`),
  CONSTRAINT `facturadetalle_ibfk_2` FOREIGN KEY (`ArticuloID`) REFERENCES `articulos` (`ArticuloID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facturadetalle`
--

LOCK TABLES `facturadetalle` WRITE;
/*!40000 ALTER TABLE `facturadetalle` DISABLE KEYS */;
INSERT INTO `facturadetalle` VALUES (1,1,2,1,45.50),(3,2,2,1,45.50),(4,2,1,3,15.00),(8,3,2,2,45.50),(9,3,1,1,15.00);
/*!40000 ALTER TABLE `facturadetalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facturas`
--

DROP TABLE IF EXISTS `facturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facturas` (
  `FacturaID` int NOT NULL AUTO_INCREMENT,
  `NumeroFactura` varchar(50) NOT NULL,
  `FechaEmision` datetime NOT NULL,
  `Subtotal` decimal(10,2) DEFAULT NULL,
  `TotalFactura` decimal(10,2) DEFAULT NULL,
  `MontoRecibido` decimal(10,2) DEFAULT NULL,
  `Vuelto` decimal(10,2) DEFAULT NULL,
  `MetodoPagoID` int DEFAULT NULL,
  PRIMARY KEY (`FacturaID`),
  UNIQUE KEY `NumeroFactura` (`NumeroFactura`),
  KEY `MetodoPagoID` (`MetodoPagoID`),
  CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`MetodoPagoID`) REFERENCES `metodopago` (`MetodoPagoID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facturas`
--

LOCK TABLES `facturas` WRITE;
/*!40000 ALTER TABLE `facturas` DISABLE KEYS */;
INSERT INTO `facturas` VALUES (1,'1756175885','2025-08-25 20:38:05',45.50,45.50,50.00,NULL,1),(2,'1756175981','2025-08-25 20:39:41',165.50,165.50,500.00,NULL,1),(3,'1756177493','2025-08-25 21:04:53',664.00,664.00,800.00,NULL,1),(4,'1756178166','2025-08-25 21:16:06',200.00,200.00,200.00,NULL,2);
/*!40000 ALTER TABLE `facturas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finanzas`
--

DROP TABLE IF EXISTS `finanzas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `finanzas` (
  `FinanzaID` int NOT NULL,
  `Fecha` date DEFAULT NULL,
  `Tipo` varchar(50) DEFAULT NULL,
  `Monto` decimal(10,2) DEFAULT NULL,
  `Descripcion` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`FinanzaID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finanzas`
--

LOCK TABLES `finanzas` WRITE;
/*!40000 ALTER TABLE `finanzas` DISABLE KEYS */;
/*!40000 ALTER TABLE `finanzas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gestionbancaria`
--

DROP TABLE IF EXISTS `gestionbancaria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gestionbancaria` (
  `BancoID` int NOT NULL,
  `NombreBanco` varchar(100) DEFAULT NULL,
  `Cuenta` varchar(50) DEFAULT NULL,
  `Saldo` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`BancoID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gestionbancaria`
--

LOCK TABLES `gestionbancaria` WRITE;
/*!40000 ALTER TABLE `gestionbancaria` DISABLE KEYS */;
/*!40000 ALTER TABLE `gestionbancaria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventariomovimientos`
--

DROP TABLE IF EXISTS `inventariomovimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventariomovimientos` (
  `MovimientoID` int NOT NULL,
  `ArticuloID` int DEFAULT NULL,
  `TipoMovimiento` varchar(50) DEFAULT NULL,
  `Cantidad` int DEFAULT NULL,
  `FechaMovimiento` date DEFAULT NULL,
  `Ubicacion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`MovimientoID`),
  KEY `inventariomovimientos_ibfk_1` (`ArticuloID`),
  CONSTRAINT `inventariomovimientos_ibfk_1` FOREIGN KEY (`ArticuloID`) REFERENCES `articulos` (`ArticuloID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventariomovimientos`
--

LOCK TABLES `inventariomovimientos` WRITE;
/*!40000 ALTER TABLE `inventariomovimientos` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventariomovimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metodopago`
--

DROP TABLE IF EXISTS `metodopago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `metodopago` (
  `MetodoPagoID` int NOT NULL,
  `Descripcion` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`MetodoPagoID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metodopago`
--

LOCK TABLES `metodopago` WRITE;
/*!40000 ALTER TABLE `metodopago` DISABLE KEYS */;
INSERT INTO `metodopago` VALUES (1,'Efectivo'),(2,'Tarjeta de Crédito'),(3,'Transferencia Bancaria');
/*!40000 ALTER TABLE `metodopago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proveedores` (
  `ProveedorID` int NOT NULL AUTO_INCREMENT,
  `NombreProveedor` varchar(100) DEFAULT NULL,
  `Contacto` varchar(100) DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Direccion` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`ProveedorID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedores`
--

LOCK TABLES `proveedores` WRITE;
/*!40000 ALTER TABLE `proveedores` DISABLE KEYS */;
INSERT INTO `proveedores` VALUES (1,'Tech Supplies','Juan Perez','555-1234','Calle Falsa 123');
/*!40000 ALTER TABLE `proveedores` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-25 22:26:17
