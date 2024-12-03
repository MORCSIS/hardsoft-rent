-- Table structure for table `acciones`
CREATE TABLE `acciones` (
  `id_accion` int(11) NOT NULL,
  `fecha_hora` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_usuario` int(11) NOT NULL,
  `accion` varchar(50) NOT NULL,
  `descripcion` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Table structure for table `accounts`
CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `name` varchar(55) NOT NULL,
  `apaterno` text NOT NULL,
  `amaterno` text NOT NULL,
  `username` varchar(10) NOT NULL,
  `password` text NOT NULL,
  `rol` int(11) NOT NULL,
  `email` text NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `session_token` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `articulos`
CREATE TABLE `articulos` (
  `id_articulo` int(11) NOT NULL,
  `nom_articulo` varchar(26) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table structure for table `clientes`
CREATE TABLE `clientes` (
  `id_cliente` int(3) NOT NULL,
  `id_empresa` int(1) DEFAULT NULL,
  `nombre_cliente` varchar(53) DEFAULT NULL,
  `cliente_rfc` varchar(13) DEFAULT NULL,
  `cliente_cp` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table structure for table `clientesequipos`
CREATE TABLE `clientesequipos` (
  `id_prim_cteequip` int(11) NOT NULL,
  `id_clienteequipo` char(50) GENERATED ALWAYS AS (concat(`id_cliente`,'-',`codigodebarras`)) STORED NOT NULL,
  `codigodebarras` varchar(50) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_estatus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- Table structure for table `dimensiones`
CREATE TABLE `dimensiones` (
  `id_dimension` int(11) NOT NULL,
  `nom_dimension` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table structure for table `discos`
CREATE TABLE `discos` (
  `id_disco` int(11) NOT NULL,
  `tipo` varchar(25) NOT NULL,
  `entrada` varchar(25) NOT NULL,
  `capacidad` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Table structure for table `empresas`
CREATE TABLE `empresas` (
  `id_empresa` int(11) NOT NULL,
  `nom_empresa` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Table structure for table `estatus`
CREATE TABLE `estatus` (
  `id_estatus` int(11) NOT NULL,
  `clave_estatus` varchar(20) NOT NULL,
  `nom_estatus` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `facturas`
CREATE TABLE `facturas` (
  `id_factura` varchar(10) DEFAULT NULL,
  `id_articulo` varchar(10) DEFAULT NULL,
  `nom_articulo` varchar(10) DEFAULT NULL,
  `cantidad_pzs` varchar(10) DEFAULT NULL,
  `precio_sin_iva` varchar(10) DEFAULT NULL,
  `iva` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table structure for table `marcas`
CREATE TABLE `marcas` (
  `id_marca` int(11) NOT NULL,
  `nom_marca` varchar(17) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table structure for table `materiales`
CREATE TABLE `materiales` (
  `id_material` int(11) NOT NULL,
  `codigodebarras` varchar(50) GENERATED ALWAYS AS (concat(`id_articulo`,`id_marca`,`id_modelo`,`id_serie`)) VIRTUAL NOT NULL,
  `id_articulo` int(11) NOT NULL,
  `id_marca` int(11) NOT NULL,
  `id_modelo` int(11) NOT NULL,
  `id_serie` int(11) NOT NULL,
  `id_estatus` int(11) NOT NULL,
  `id_procesador` int(11) NOT NULL,
  `id_disco` int(11) NOT NULL,
  `id_memoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `memorias`
CREATE TABLE `memorias` (
  `id_memoria` int(11) NOT NULL,
  `nom_memoria` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table structure for table `modelos`
CREATE TABLE `modelos` (
  `id_modelo` int(11) NOT NULL,
  `nom_modelo` varchar(21) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table structure for table `procesadores`
CREATE TABLE `procesadores` (
  `id_procesador` int(11) NOT NULL,
  `nom_procesador` varchar(20) NOT NULL,
  `generacion` varchar(10) NOT NULL,
  `velocidad` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table structure for table `proveedores`
CREATE TABLE `proveedores` (
  `_proveedor` int(11) DEFAULT NULL,
  `nom_proveedor` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table structure for table `roles`
CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nom_rol` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Table structure for table `series`
CREATE TABLE `series` (
  `id_serie` int(11) NOT NULL,
  `nom_serie` char(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `sos`
CREATE TABLE `sos` (
  `id_so` int(11) NOT NULL,
  `nom_so` varchar(14) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table structure for table `transacciones`
CREATE TABLE `transacciones` (
  `id_transaccion` int(11) NOT NULL,
  `nombre_transaccion` char(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Table structure for table `usuarios`
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nom_usuario` varchar(20) NOT NULL,
  `desc_usuario` varchar(155) NOT NULL,
  `tipo_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
