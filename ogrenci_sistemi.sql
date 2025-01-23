-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1:3306
-- Üretim Zamanı: 21 Oca 2025, 10:37:39
-- Sunucu sürümü: 8.2.0
-- PHP Sürümü: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `ogrenci_sistemi`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ogrenciler`
--

DROP TABLE IF EXISTS `ogrenciler`;
CREATE TABLE IF NOT EXISTS `ogrenciler` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ad` varchar(100) NOT NULL,
  `soyad` varchar(100) NOT NULL,
  `yas` int NOT NULL,
  `eklenme_tarihi` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `ogrenciler`
--

INSERT INTO `ogrenciler` (`id`, `ad`, `soyad`, `yas`, `eklenme_tarihi`) VALUES
(1, 'Emre', 'Yılmazoğlu', 20, '2025-01-20 23:04:02'),
(3, 'Buğra Göksel', 'Şenler', 21, '2025-01-20 23:04:02'),
(4, 'Burak ', 'Akdağ', 20, '2025-01-20 23:04:02'),
(5, 'Emirhan', 'Çalışkan', 20, '2025-01-20 23:04:02'),
(6, 'Fadime', 'Şentürk', 22, '2025-01-20 23:04:02'),
(7, 'Beyza', 'Arıcı', 23, '2025-01-20 23:04:02'),
(8, 'Emre', 'Bircan', 18, '2025-01-20 23:09:28'),
(9, 'Salih', 'Yüksel', 19, '2025-01-20 23:09:47'),
(2, 'Mustafa', 'Gürpınar', 27, '2025-01-21 01:59:10');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
