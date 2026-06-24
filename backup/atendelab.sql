-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 24, 2026 at 11:25 PM
-- Server version: 8.0.46
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `atendelab`
--

-- --------------------------------------------------------

--
-- Table structure for table `atendimentos`
--

CREATE TABLE `atendimentos` (
  `id` int NOT NULL,
  `pessoa_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `tipo_atendimento_id` int NOT NULL,
  `data_atendimento` date NOT NULL,
  `hora_atendimento` time DEFAULT NULL,
  `descricao` text COLLATE utf8mb4_general_ci,
  `observacao` text COLLATE utf8mb4_general_ci,
  `status` enum('aberto','em_andamento','concluido') COLLATE utf8mb4_general_ci DEFAULT 'aberto',
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `atendimentos`
--

INSERT INTO `atendimentos` (`id`, `pessoa_id`, `usuario_id`, `tipo_atendimento_id`, `data_atendimento`, `hora_atendimento`, `descricao`, `observacao`, `status`, `criado_em`) VALUES
(1, 1, 1, 1, '2026-06-11', NULL, 'Aluno solicitou a troca de curso de Engenharia de Software para Artes Visuais', 'Aluno estava no primeiro semestre', 'aberto', '2026-06-12 00:24:58');

-- --------------------------------------------------------

--
-- Table structure for table `pessoas`
--

CREATE TABLE `pessoas` (
  `id` int NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `cpf` char(11) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `curso` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `periodo` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('ativo','inativo') COLLATE utf8mb4_general_ci DEFAULT 'ativo',
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pessoas`
--

INSERT INTO `pessoas` (`id`, `nome`, `cpf`, `telefone`, `email`, `curso`, `periodo`, `status`, `criado_em`) VALUES
(1, 'Fernando da Silva', '12345678901', '47999999999', 'fernando@gmail.com', '', '', 'ativo', '2026-06-12 00:21:38');

-- --------------------------------------------------------

--
-- Table structure for table `tipos_atendimentos`
--

CREATE TABLE `tipos_atendimentos` (
  `id` int NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_general_ci,
  `status` enum('ativo','inativo') COLLATE utf8mb4_general_ci DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tipos_atendimentos`
--

INSERT INTO `tipos_atendimentos` (`id`, `nome`, `descricao`, `status`) VALUES
(1, 'Trocar de curso', 'Destinado para a troca de curso dos alunos presentes.', 'ativo');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `perfil` enum('admin','atendente') COLLATE utf8mb4_general_ci DEFAULT 'atendente',
  `status` enum('ativo','inativo') COLLATE utf8mb4_general_ci DEFAULT 'ativo',
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `perfil`, `status`, `criado_em`) VALUES
(1, 'Administrador', 'admin@atendelab.com', '$2y$10$9/j34sbO7V6EFgMOeBx/wepkqJfXkwPA2av.a17mAc2f4zbsQan8G', 'admin', 'ativo', '2026-06-11 23:33:07'),
(2, 'Vitor Henrique da Silveira Alano', 'vitor@gmail.com', '$2y$10$ON1BctigjvU00TKbUTwHhuvMzWy27revbx2jKNz5BHgnz8uVAh9hS', 'admin', 'inativo', '2026-06-12 00:19:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `atendimentos`
--
ALTER TABLE `atendimentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pessoa_id` (`pessoa_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `tipo_atendimento_id` (`tipo_atendimento_id`);

--
-- Indexes for table `pessoas`
--
ALTER TABLE `pessoas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Indexes for table `tipos_atendimentos`
--
ALTER TABLE `tipos_atendimentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `atendimentos`
--
ALTER TABLE `atendimentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pessoas`
--
ALTER TABLE `pessoas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tipos_atendimentos`
--
ALTER TABLE `tipos_atendimentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `atendimentos`
--
ALTER TABLE `atendimentos`
  ADD CONSTRAINT `atendimentos_ibfk_1` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoas` (`id`),
  ADD CONSTRAINT `atendimentos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `atendimentos_ibfk_3` FOREIGN KEY (`tipo_atendimento_id`) REFERENCES `tipos_atendimentos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
