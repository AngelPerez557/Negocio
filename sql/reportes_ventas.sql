-- Total vendido hoy
SELECT IFNULL(SUM(TotalFactura),0) AS total_hoy
FROM facturas
WHERE DATE(FechaEmision) = CURDATE();

-- Total vendido esta semana (semana actual, desde lunes)
SELECT IFNULL(SUM(TotalFactura),0) AS total_semana
FROM facturas
WHERE YEARWEEK(FechaEmision, 1) = YEARWEEK(CURDATE(), 1);

-- Total vendido este mes
SELECT IFNULL(SUM(TotalFactura),0) AS total_mes
FROM facturas
WHERE YEAR(FechaEmision) = YEAR(CURDATE())
  AND MONTH(FechaEmision) = MONTH(CURDATE());

-- Total vendido este año
SELECT IFNULL(SUM(TotalFactura),0) AS total_anio
FROM facturas
WHERE YEAR(FechaEmision) = YEAR(CURDATE());
