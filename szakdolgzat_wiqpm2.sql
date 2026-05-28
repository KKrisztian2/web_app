-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2025. Nov 19. 16:36
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `szakdolgzat_wiqpm2`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `csoportok`
--

CREATE TABLE `csoportok` (
  `id` int(11) NOT NULL,
  `oktato_id` int(11) DEFAULT NULL,
  `nev` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- A tábla adatainak kiíratása `csoportok`
--

INSERT INTO `csoportok` (`id`, `oktato_id`, `nev`) VALUES
(1, 1, 'csoport1'),
(2, 1, 'csoport_neve'),
(3, 3, 'Tamas_csoportja'),
(4, 1, 'teszt_csoport5'),
(5, 1, 'teszt_csoport2');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `csoporttagok`
--

CREATE TABLE `csoporttagok` (
  `felhasznalo_id` int(11) NOT NULL,
  `csoport_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- A tábla adatainak kiíratása `csoporttagok`
--

INSERT INTO `csoporttagok` (`felhasznalo_id`, `csoport_id`) VALUES
(2, 1),
(2, 2),
(2, 3),
(4, 1),
(4, 2),
(4, 5),
(5, 1),
(6, 4);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `eredmenyek`
--

CREATE TABLE `eredmenyek` (
  `id` int(11) NOT NULL,
  `diak_id` text CHARACTER SET utf32 COLLATE utf32_bin DEFAULT NULL,
  `feladatsor_id` int(11) DEFAULT NULL,
  `pontszam` int(11) DEFAULT NULL,
  `szazalek` int(11) DEFAULT NULL,
  `datum` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- A tábla adatainak kiíratása `eredmenyek`
--

INSERT INTO `eredmenyek` (`id`, `diak_id`, `feladatsor_id`, `pontszam`, `szazalek`, `datum`) VALUES
(1, '2', 1, 1, 33, '2025-04-01 00:00:00'),
(2, '4', 1, 1, 33, '2025-04-01 00:00:00'),
(3, '1', 2, 0, 0, '2025-05-31 00:00:00'),
(4, '2', 2, 0, 0, '2025-05-31 00:00:00'),
(5, '1', 2, 0, 0, '2025-05-31 00:00:00'),
(6, '1', 2, 2, 25, '2025-05-31 00:00:00'),
(7, '2', 2, 5, 63, '2025-05-31 00:00:00'),
(8, '2', 2, 0, 0, '2025-05-31 00:00:00'),
(9, '2', 2, 0, 0, '2025-10-07 00:00:00'),
(10, '1', 2, 0, 0, '2025-10-27 00:00:00'),
(11, '2', 2, 0, 0, '2025-10-30 05:16:36'),
(25, '2', 2, 1, 50, '2025-10-30 05:53:48'),
(29, '2', 2, 1, 50, '2025-10-30 06:14:35'),
(30, '2', 2, 1, 38, '2025-10-30 06:17:21'),
(31, '2', 2, 2, 75, '2025-10-30 06:19:33'),
(32, '2', 2, 1, 63, '2025-10-30 06:20:27'),
(33, '2', 2, 2, 75, '2025-10-30 06:21:53'),
(34, '2', 2, 3, 83, '2025-10-30 06:24:22'),
(35, '2', 2, 2, 67, '2025-10-30 06:29:25'),
(36, 'WIQPM2', 1, 2, 40, '2025-11-02 03:49:44'),
(37, '1', 2, 1, 42, '2025-11-06 03:10:17'),
(38, 'ismeretlen', 2, 1, 42, '2025-11-06 06:01:41'),
(39, 'ismeretlen', 2, 1, 33, '2025-11-06 12:54:07');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `feladatok`
--

CREATE TABLE `feladatok` (
  `id` int(11) NOT NULL,
  `szoveg` varchar(255) NOT NULL,
  `tipus` int(11) NOT NULL,
  `valaszok_szama` int(11) DEFAULT 1,
  `megoldas` varchar(255) DEFAULT NULL,
  `temakor` varchar(100) DEFAULT NULL,
  `nyilvanos` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- A tábla adatainak kiíratása `feladatok`
--

INSERT INTO `feladatok` (`id`, `szoveg`, `tipus`, `valaszok_szama`, `megoldas`, `temakor`, `nyilvanos`) VALUES
(1, '<p>Feladat teszt 1</p>', 3, 2, '21	1', 'Térmértan', 1),
(2, 'Feladat teszt 2', 2, 4, 'A	C	D	B', 'Térmértan', 1),
(3, 'Feladat teszt 3', 1, 1, 'C', 'Kinetika', 1),
(4, 'Feladat teszt 4.', 3, 1, '2', 'Térmértan', 2),
(5, '<p>Feleletv&aacute;laszt&oacute;s feladat.<br />A) A<br />B) B<br />C) C<br />D) D</p>', 1, 1, 'C', 'Kinetika', 1),
(6, '<p>Egyszer? feladat, A a v&aacute;lasz.<br />A) A<br />B) B<br />C) 12<br />D) D</p>', 1, 1, 'A', 'Kinetika', 1),
(7, '<p>Els? feladat a feladatsorban.</p>', 1, 1, 'B', 'Térmértan', 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `feladatsorok`
--

CREATE TABLE `feladatsorok` (
  `id` int(11) NOT NULL,
  `nev` varchar(100) NOT NULL,
  `szerkesztette` int(11) DEFAULT NULL,
  `allapot` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- A tábla adatainak kiíratása `feladatsorok`
--

INSERT INTO `feladatsorok` (`id`, `nev`, `szerkesztette`, `allapot`) VALUES
(1, 'feladatsorom_g', 1, 'zart'),
(2, 'Bam', 1, 'nyilvanos'),
(7, 'test_fel', 1, 'zart'),
(8, 'open', 3, 'nyilvanos'),
(10, 'Feladatsor 3', 1, 'zart');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `felhasznalok`
--

CREATE TABLE `felhasznalok` (
  `id` int(11) NOT NULL,
  `azonosito` varchar(50) DEFAULT NULL,
  `nev` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `rang` varchar(50) NOT NULL,
  `jelszo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- A tábla adatainak kiíratása `felhasznalok`
--

INSERT INTO `felhasznalok` (`id`, `azonosito`, `nev`, `email`, `rang`, `jelszo`) VALUES
(1, 'AZON12', 'Szilágyi Lajos', 'lajos@gmail.com', 'tanar', '12345678'),
(2, 'T3RD4S', 'Minta Diák', 'diakvagyok@gmail.com', 'diak', 'qwertzui'),
(3, 'OKTTAM', 'Oktató Tamás', 'tams1@gmail.com', 'tanar', '12345678'),
(4, 'QWERT6', 'Deak Diak', 'diak@diakmail.com', 'diak', '12345678'),
(5, '5', 'Hello Szia', 'sziahello@gmail.com', 'diak', '12345678'),
(6, 'HFNDV3', 'Uj Diak', 'ujdiaktestmail@gmail.com', 'diak', '$2y$10$w.7LSCVtX37nRtkgR0k3R.ZhGb22f9udSbXVRGvaHsWOCYmlWX9VC');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `kerdes_feladat`
--

CREATE TABLE `kerdes_feladat` (
  `feladatsor_id` int(11) NOT NULL,
  `kerdes_id` int(11) NOT NULL,
  `pont` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- A tábla adatainak kiíratása `kerdes_feladat`
--

INSERT INTO `kerdes_feladat` (`feladatsor_id`, `kerdes_id`, `pont`) VALUES
(1, 1, 2),
(1, 2, 1),
(1, 5, 1),
(1, 6, 1),
(2, 1, 1),
(2, 2, 1),
(2, 6, 1),
(10, 7, 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `kiadott_feladatsorok`
--

CREATE TABLE `kiadott_feladatsorok` (
  `feladatsor_id` int(11) NOT NULL,
  `csoport_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- A tábla adatainak kiíratása `kiadott_feladatsorok`
--

INSERT INTO `kiadott_feladatsorok` (`feladatsor_id`, `csoport_id`) VALUES
(1, 2),
(1, 4),
(2, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `szoba`
--

CREATE TABLE `szoba` (
  `id` int(11) NOT NULL,
  `feladatsor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- A tábla adatainak kiíratása `szoba`
--

INSERT INTO `szoba` (`id`, `feladatsor`) VALUES
(6542, 1);

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `csoportok`
--
ALTER TABLE `csoportok`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oktato_id` (`oktato_id`);

--
-- A tábla indexei `csoporttagok`
--
ALTER TABLE `csoporttagok`
  ADD PRIMARY KEY (`felhasznalo_id`,`csoport_id`),
  ADD KEY `csoport_id` (`csoport_id`);

--
-- A tábla indexei `eredmenyek`
--
ALTER TABLE `eredmenyek`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feladatsor_id` (`feladatsor_id`);

--
-- A tábla indexei `feladatok`
--
ALTER TABLE `feladatok`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `feladatsorok`
--
ALTER TABLE `feladatsorok`
  ADD PRIMARY KEY (`id`),
  ADD KEY `szerkesztette` (`szerkesztette`);

--
-- A tábla indexei `felhasznalok`
--
ALTER TABLE `felhasznalok`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- A tábla indexei `kerdes_feladat`
--
ALTER TABLE `kerdes_feladat`
  ADD PRIMARY KEY (`feladatsor_id`,`kerdes_id`),
  ADD KEY `kerdes_id` (`kerdes_id`);

--
-- A tábla indexei `kiadott_feladatsorok`
--
ALTER TABLE `kiadott_feladatsorok`
  ADD PRIMARY KEY (`feladatsor_id`,`csoport_id`),
  ADD KEY `csoport_id` (`csoport_id`);

--
-- A tábla indexei `szoba`
--
ALTER TABLE `szoba`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feladatsor` (`feladatsor`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `csoportok`
--
ALTER TABLE `csoportok`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT a táblához `eredmenyek`
--
ALTER TABLE `eredmenyek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT a táblához `feladatok`
--
ALTER TABLE `feladatok`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT a táblához `feladatsorok`
--
ALTER TABLE `feladatsorok`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT a táblához `felhasznalok`
--
ALTER TABLE `felhasznalok`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `csoportok`
--
ALTER TABLE `csoportok`
  ADD CONSTRAINT `csoportok_ibfk_1` FOREIGN KEY (`oktato_id`) REFERENCES `felhasznalok` (`id`);

--
-- Megkötések a táblához `csoporttagok`
--
ALTER TABLE `csoporttagok`
  ADD CONSTRAINT `csoporttagok_ibfk_1` FOREIGN KEY (`felhasznalo_id`) REFERENCES `felhasznalok` (`id`),
  ADD CONSTRAINT `csoporttagok_ibfk_2` FOREIGN KEY (`csoport_id`) REFERENCES `csoportok` (`id`);

--
-- Megkötések a táblához `feladatsorok`
--
ALTER TABLE `feladatsorok`
  ADD CONSTRAINT `feladatsorok_ibfk_1` FOREIGN KEY (`szerkesztette`) REFERENCES `felhasznalok` (`id`);

--
-- Megkötések a táblához `kerdes_feladat`
--
ALTER TABLE `kerdes_feladat`
  ADD CONSTRAINT `kerdes_feladat_ibfk_1` FOREIGN KEY (`kerdes_id`) REFERENCES `feladatok` (`id`),
  ADD CONSTRAINT `kerdes_feladat_ibfk_2` FOREIGN KEY (`feladatsor_id`) REFERENCES `feladatsorok` (`id`);

--
-- Megkötések a táblához `kiadott_feladatsorok`
--
ALTER TABLE `kiadott_feladatsorok`
  ADD CONSTRAINT `kiadott_feladatsorok_ibfk_1` FOREIGN KEY (`feladatsor_id`) REFERENCES `feladatsorok` (`id`),
  ADD CONSTRAINT `kiadott_feladatsorok_ibfk_2` FOREIGN KEY (`csoport_id`) REFERENCES `csoportok` (`id`);

--
-- Megkötések a táblához `szoba`
--
ALTER TABLE `szoba`
  ADD CONSTRAINT `szoba_ibfk_1` FOREIGN KEY (`feladatsor`) REFERENCES `feladatsorok` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
