grant all on qvantel.* to root@localhost identified by 'tetramou';


drop database if exists qvantel;
create database qvantel;
use qvantel;

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) UNIQUE NOT NULL,
  `descr` varchar(255) NOT NULL,
  `price` double NOT NULL,
  INDEX nameIndex (name),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 collate utf8_swedish_ci COMMENT='QShop by THa';

CREATE TABLE IF NOT EXISTS `stock` (
  `stockid` int(11) NOT NULL AUTO_INCREMENT,
  `productid` int(11) NOT NULL default '0',
  `amount` int(11) NOT NULL default '0',
  `reserved` int(11) NOT NULL default '0',
  PRIMARY KEY (`stockid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 collate utf8_swedish_ci COMMENT='QShop by THa';

CREATE TABLE IF NOT EXISTS `basket` (
  `basketid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `pcs` int NOT NULL default '0',
  PRIMARY KEY (`basketid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 collate utf8_swedish_ci COMMENT='QShop by THa';

INSERT INTO products (id, name,descr,price) VALUES
(1,'SAMA510GD','Samsung Galaxy A5 (2016) älypuhelin (kulta)',969),
(2,'SAMA510BK','Samsung Galaxy A5 (2016) älypuhelin (musta)',249),
(3,'SAMA510sv','Samsung Galaxy A5 (2016) älypuhelin (hopea)',399),
(4,'SAMA510gr','Samsung Galaxy A5 (2016) älypuhelin (vihreä)',499),
(5,'SAMA510cn','Samsung Galaxy A5 (2016) älypuhelin (cyan)',599),
(6,'SAMA510red','Samsung Galaxy A5 (2016) älypuhelin (punainen)',99),
(7,'SAMA510white','Samsung Galaxy A5 (2016) älypuhelin (valkea)',59),
(8,'SAMA510black','Samsung Galaxy A5 (2016) älypuhelin (mustaa)',199),
(9,'SAMA510grey','Samsung Galaxy A5 (2016) älypuhelin (harmaa)',299),
(10,'SAMA510hollow','Samsung Galaxy A5 (2016) älypuhelin (ontto)',19),

(11,'IP5S32GBSV','IPhone 5S 32GB',469),
(12,'IP6S32GBBK','IPhone 6S 34GB',1249),
(13,'IP6S32GBAK','IPhone 6S 44GB',1399),
(14,'IP6S32GBDD','IPhone 6S 55GB',1499),
(15,'IP6S32GBFF','IPhone 6S 66GB',1599),
(16,'IP6S32GBGG','IPhone 6S 77GB',199),
(17,'IP6S32GBEE','IPhone 6S 88GB',159),
(18,'IP6S32GBRR','IPhone 6S 99GB',1199),
(19,'IP6S32GBWW','IPhone 6S 100GB',1299),
(20,'IP6S32GBXX','IPhone 6S 132GB',119),

(21,'HUAHON7SI','Huawei Honor 7 älypuhelin',111),
(22,'HUAHON6BS','Huawei Honor 6 älypuhelin',229),
(23,'HUAHON6BF','Huawei Honor 7 älypuhelin',329),
(24,'HUAHON6BE','Huawei Honor 7 älypuhelin',429),
(25,'HUAHON6BR','Huawei Honor 7 älypuhelin',529),
(26,'HUAP8LBK','Huawei P8 Lite musta',129),
(27,'HUAP8LRD','Huawei P8 Lite punainen',129),
(28,'HUAP8LGR','Huawei P8 Lite vihreä',229),
(29,'HUAP8LSV','Huawei P8 Lite hopea',329),
(30,'HUANEX64SI','Huawei Nexus 6P 65GB',219);

// Kayttajat, tahan tallennetaan session id usean kayttajan simulointiin
CREATE TABLE IF NOT EXISTS `users` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(60) UNIQUE NOT NULL,
  `password` varchar(60) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 collate utf8_swedish_ci COMMENT='QShop by THa';

INSERT INTO users (user,apikey) VALUES
('qshopuser','solutions');
