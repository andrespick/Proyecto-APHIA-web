SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

CREATE DATABASE IF NOT EXISTS `aphia_p`;
USE `aphia_p`;

CREATE TABLE `PERSON` (
    `personId` int(11) NOT NULL AUTO_INCREMENT,
    `personCategory` varchar(50) DEFAULT NULL COMMENT 'Owner, Tenant, Co-Debtor',
    `fullName` varchar(255) DEFAULT NULL,
    `documentCategory` varchar(10) DEFAULT NULL COMMENT 'ID, TI, CE, PAS, etc.',
    `documentIdentifier` varchar(50) DEFAULT NULL,
    `address` varchar(255) DEFAULT NULL,
    `phoneNumber` varchar(50) DEFAULT NULL,
    `phonePrefix` varchar(100) DEFAULT NULL COMMENT 'e.g. +57, +1, +34, etc.',
    `emailAddress` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`personId`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO `PERSON` (
    `personId`,
    `personCategory`,
    `fullName`,
    `documentCategory`,
    `documentIdentifier`,
    `address`,
    `phoneNumber`,
    `phonePrefix`,
    `emailAddress`
) VALUES
    (
        0,
        'Owner',
        'andes felipe vela florez',
        'CE',
        '1193399190',
        'Cll 3  BIS  Oeste # 83  - 13    TO 5 apto 204',
        '3116402379',
        NULL,
        'andresfelipevelaflorez@gmail.com'
    ),
    (
        1,
        'Cliente',
        'Juan Camilo Torres',
        'CC',
        '1025487963',
        'Cra 45 #12-34, Cali',
        '3112567890',
        '+57',
        'juancamilo.torres@gmail.com'
    ),
    (
        2,
        'Cliente',
        'María Fernanda Rojas',
        'CC',
        '1032458796',
        'Calle 10 #5-22, Cali',
        '3125987412',
        '+57',
        'maria.rojas@hotmail.com'
    ),
    (
        3,
        'Propietario',
        'Carlos Andrés López',
        'CC',
        '9876543210',
        'Av. 6N #34-56, Cali',
        '3002589631',
        '+57',
        'carlos.lopez@outlook.com'
    ),
    (
        4,
        'Cliente',
        'Diana Carolina Pérez',
        'CC',
        '1047859632',
        'Cra 8 #45-21, Cali',
        '3168524790',
        '+57',
        'dianacp@gmail.com'
    ),
    (
        5,
        'Codeudor',
        'Jorge Iván Castillo',
        'CC',
        '1052347896',
        'Calle 13 #25-30, Jamundí',
        '3154789621',
        '+57',
        'jorge.castillo@gmail.com'
    ),
    (
        6,
        'Propietario',
        'Laura Vanessa Molina',
        'CC',
        '1069854721',
        'Av. Roosevelt #30-21, Cali',
        '3201458796',
        '+57',
        'laura.molina@yahoo.com'
    ),
    (
        7,
        'Cliente',
        'Andrés Felipe Martínez',
        'CC',
        '1078542396',
        'Cra 7 #20-10, Palmira',
        '3184523698',
        '+57',
        'andres.mtz@gmail.com'
    ),
    (
        8,
        'Codeudor',
        'Paola Andrea Sánchez',
        'CC',
        '1087459632',
        'Calle 4 #18-09, Cali',
        '3107896541',
        '+57',
        'paola.sanchez@gmail.com'
    ),
    (
        9,
        'Propietario',
        'Ricardo Gómez Patiño',
        'CC',
        '1098745632',
        'Cra 12 #33-45, Yumbo',
        '3124789650',
        '+57',
        'ricardo.gomez@correo.com'
    ),
    (
        10,
        'Cliente',
        'Liliana Méndez Ortega',
        'CC',
        '1109854732',
        'Calle 44 #8-60, Cali',
        '3114527896',
        '+57',
        'liliana.mendez@gmail.com'
    ),
    (
        11,
        'Cliente',
        'Santiago Ortiz Ruiz',
        'TI',
        '1123456789',
        'Cra 9 #23-15, Cali',
        '3178956210',
        '+57',
        'santiago.ortiz@gmail.com'
    ),
    (
        12,
        'Propietario',
        'Angela María Herrera',
        'CC',
        '1132659874',
        'Calle 7 #11-22, Cali',
        '3198754632',
        '+57',
        'angela.herrera@outlook.com'
    ),
    (
        13,
        'Codeudor',
        'David Esteban Rivas',
        'CC',
        '1148965237',
        'Cra 2 #45-90, Cali',
        '3124587963',
        '+57',
        'david.rivas@gmail.com'
    ),
    (
        14,
        'Cliente',
        'Camila Alejandra Vélez',
        'CC',
        '1158742369',
        'Av. Pasoancho #55-20, Cali',
        '3154789632',
        '+57',
        'camila.velez@gmail.com'
    ),
    (
        15,
        'Propietario',
        'Oscar Hernán Zapata',
        'CC',
        '1165897423',
        'Cra 50 #9-12, Cali',
        '3107458963',
        '+57',
        'oscar.zapata@gmail.com'
    ),
    (
        16,
        'Cliente',
        'Natalia Ramírez Ospina',
        'CC',
        '1204587963',
        'Calle 23 #45-67, Cali',
        '3115698741',
        '+57',
        'natalia.ramirez@gmail.com'
    ),
    (
        17,
        'Cliente',
        'Sebastián Gómez Lara',
        'CC',
        '1214789654',
        'Cra 7 #18-22, Palmira',
        '3187452369',
        '+57',
        'sebastian.gomez@hotmail.com'
    ),
    (
        18,
        'Cliente',
        'Valentina Muñoz Ortiz',
        'CC',
        '1225698745',
        'Av. Pasoancho #55-32, Cali',
        '3178945632',
        '+57',
        'valentina.munoz@yahoo.com'
    ),
    (
        19,
        'Cliente',
        'Julián Esteban Rojas',
        'CC',
        '1234785962',
        'Calle 9 #12-10, Cali',
        '3124789563',
        '+57',
        'julian.rojas@gmail.com'
    ),
    (
        20,
        'Cliente',
        'Daniela Pérez Castaño',
        'CC',
        '1245896321',
        'Cra 3 #45-98, Cali',
        '3154789632',
        '+57',
        'daniela.perez@hotmail.com'
    ),
    (
        21,
        'Cliente',
        'Camilo Andrés Torres',
        'CC',
        '1258963214',
        'Av. Roosevelt #12-30, Cali',
        '3107458963',
        '+57',
        'camilo.torres@gmail.com'
    ),
    (
        22,
        'Cliente',
        'Sara Lucía Fernández',
        'CC',
        '1269854785',
        'Calle 13 #45-20, Cali',
        '3204587963',
        '+57',
        'sara.fernandez@yahoo.com'
    ),
    (
        23,
        'Cliente',
        'Andrés Mauricio Lozano',
        'CC',
        '1278963542',
        'Cra 6 #34-19, Yumbo',
        '3137854963',
        '+57',
        'andres.lozano@gmail.com'
    ),
    (
        24,
        'Cliente',
        'Carolina Herrera Castro',
        'CC',
        '1287459631',
        'Calle 8 #40-21, Cali',
        '3189654785',
        '+57',
        'carolina.herrera@hotmail.com'
    ),
    (
        25,
        'Cliente',
        'Felipe Restrepo López',
        'CC',
        '1296589742',
        'Av. Las Américas #25-10, Cali',
        '3124569789',
        '+57',
        'felipe.restrepo@gmail.com'
    ),
    (
        26,
        'Cliente',
        'Luisa Fernanda Prieto',
        'CC',
        '1304789652',
        'Cra 10 #50-22, Cali',
        '3114789654',
        '+57',
        'luisa.prieto@gmail.com'
    ),
    (
        27,
        'Cliente',
        'Jorge Enrique Martínez',
        'CC',
        '1315698745',
        'Calle 25 #18-40, Cali',
        '3147859632',
        '+57',
        'jorge.martinez@hotmail.com'
    ),
    (
        28,
        'Cliente',
        'Melissa Andrea Cárdenas',
        'CC',
        '1324789561',
        'Cra 4 #22-15, Cali',
        '3168745963',
        '+57',
        'melissa.cardenas@gmail.com'
    ),
    (
        29,
        'Cliente',
        'Santiago David Castro',
        'CC',
        '1335896472',
        'Calle 32 #45-12, Cali',
        '3158964785',
        '+57',
        'santiago.castro@gmail.com'
    ),
    (
        30,
        'Cliente',
        'Isabella Gómez Ruiz',
        'CC',
        '1346987521',
        'Av. 3N #12-25, Cali',
        '3178459632',
        '+57',
        'isabella.gomez@yahoo.com'
    );

CREATE TABLE `USER_ACCOUNT` (
    `accountId` int(11) NOT NULL AUTO_INCREMENT,
    `userName` varchar(255) DEFAULT NULL,
    `documentCategory` varchar(10) DEFAULT NULL,
    `documentIdentifier` varchar(50) DEFAULT NULL,
    `emailAddress` varchar(255) DEFAULT NULL,
    `hashedPassword` varchar(255) DEFAULT NULL COMMENT 'Hashed password',
    `userCategory` varchar(50) DEFAULT NULL COMMENT 'Administrator, Staff, Advisor',
    PRIMARY KEY (`accountId`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO `USER_ACCOUNT` (
    `accountId`,
    `userName`,
    `documentCategory`,
    `documentIdentifier`,
    `emailAddress`,
    `hashedPassword`,
    `userCategory`
) VALUES
    (
        1,
        'testsysadmin',
        'ID',
        '98765432',
        'testadmin@example.com',
        'password123',
        'System Administrator'
    ),
    (
        2,
        'testadmin',
        'ID',
        '98765432',
        'testadmin1@example.com',
        'password124',
        'administrator'
    ),
    (
        3,
        'testAdvisor',
        'ID',
        '98765432',
        'testadmin@example.com',
        'password123',
        'Advisor'
    );

CREATE TABLE `PROPERTY` (
    `propertyId` int(11) NOT NULL AUTO_INCREMENT,
    `address` varchar(255) DEFAULT NULL,
    `city` varchar(255) DEFAULT NULL,
    `registrationIdentifier` varchar(255) DEFAULT NULL,
    `rentalValue` decimal(12, 2) DEFAULT NULL,
    `utilityContractIdentifiers` varchar(255) DEFAULT NULL COMMENT 'Utility service contract identifiers',
    `occupancyState` varchar(255) DEFAULT NULL,
    `propertyType` varchar(50) DEFAULT NULL COMMENT 'Tipo de inmueble: Apartamento, Casa, Oficina, etc.',
    `ownerId` int(11) NOT NULL,
    PRIMARY KEY (`propertyId`),
    KEY `ownerId` (`ownerId`),
    CONSTRAINT `PROPERTY_ibfk_1` FOREIGN KEY (`ownerId`) REFERENCES `PERSON` (`personId`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE `BANK_ACCOUNT` (
    `accountId` int(11) NOT NULL AUTO_INCREMENT,
    `personId` int(11) NOT NULL,
    `accountIdentifier` varchar(50) DEFAULT NULL,
    `accountCategory` varchar(50) DEFAULT NULL COMMENT 'Savings, Checking, etc.',
    `financialInstitution` varchar(100) DEFAULT NULL,
    PRIMARY KEY (`accountId`),
    KEY `personId` (`personId`),
    CONSTRAINT `BANK_ACCOUNT_ibfk_1` FOREIGN KEY (`personId`) REFERENCES `PERSON` (`personId`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO `BANK_ACCOUNT` (
    `accountId`,
    `personId`,
    `accountIdentifier`,
    `accountCategory`,
    `financialInstitution`
) VALUES (
    0,
    0,
    '111111111111111',
    'Ahorros',
    'Bancolombia'
);

CREATE TABLE `PROPERTY_AGREEMENT` (
    `agreementId` int(11) NOT NULL AUTO_INCREMENT,
    `agreementCategory` varchar(50) DEFAULT NULL COMMENT 'Lease, Brokerage, PowerOfAttorney',
    `agreementContent` longtext DEFAULT NULL COMMENT 'Full agreement content',
    `startDate` date DEFAULT NULL,
    `endDate` date DEFAULT NULL,
    `conditions` text DEFAULT NULL,
    `propertyId` int(11) DEFAULT NULL,
    `associatedPersonId` int(11) NOT NULL COMMENT 'Tenant or Owner depending on agreement category',
    PRIMARY KEY (`agreementId`),
    KEY `propertyId` (`propertyId`),
    KEY `associatedPersonId` (`associatedPersonId`),
    CONSTRAINT `PROPERTY_AGREEMENT_ibfk_1` FOREIGN KEY (`propertyId`) REFERENCES `PROPERTY` (`propertyId`),
    CONSTRAINT `PROPERTY_AGREEMENT_ibfk_2` FOREIGN KEY (`associatedPersonId`) REFERENCES `PERSON` (`personId`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE `AGREEMENT_CO_DEBTOR` (
    `agreementId` int(11) NOT NULL,
    `coDebtorId` int(11) NOT NULL,
    PRIMARY KEY (`agreementId`, `coDebtorId`),
    KEY `coDebtorId` (`coDebtorId`),
    CONSTRAINT `AGREEMENT_CO_DEBTOR_ibfk_1` FOREIGN KEY (`agreementId`) REFERENCES `PROPERTY_AGREEMENT` (`agreementId`),
    CONSTRAINT `AGREEMENT_CO_DEBTOR_ibfk_2` FOREIGN KEY (`coDebtorId`) REFERENCES `PERSON` (`personId`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE `FILE_RECORD` (
    `fileId` int(11) NOT NULL AUTO_INCREMENT,
    `fileName` varchar(255) DEFAULT NULL,
    `fileCategory` varchar(255) DEFAULT NULL COMMENT 'Agreement, ID, Deed, etc.',
    `filePath` varchar(255) DEFAULT NULL COMMENT 'Digital file location',
    `uploadDate` date DEFAULT NULL,
    `personId` int(11) DEFAULT NULL,
    `propertyId` int(11) DEFAULT NULL,
    PRIMARY KEY (`fileId`),
    KEY `personId` (`personId`),
    KEY `propertyId` (`propertyId`),
    CONSTRAINT `FILE_RECORD_ibfk_1` FOREIGN KEY (`personId`) REFERENCES `PERSON` (`personId`),
    CONSTRAINT `FILE_RECORD_ibfk_2` FOREIGN KEY (`propertyId`) REFERENCES `PROPERTY` (`propertyId`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE `FINANCIAL_RECORD` (
    `recordId` int(11) NOT NULL AUTO_INCREMENT,
    `recordCategory` varchar(255) DEFAULT NULL COMMENT 'Rent payment, administration fee, property repair, etc.',
    `amount` decimal(12, 2) DEFAULT NULL,
    `recordDate` date DEFAULT NULL,
    `paymentState` varchar(255) DEFAULT NULL COMMENT 'Pending, Overdue, Paid, etc.',
    `agreementId` int(11) NOT NULL,
    PRIMARY KEY (`recordId`),
    KEY `agreementId` (`agreementId`),
    CONSTRAINT `FINANCIAL_RECORD_ibfk_1` FOREIGN KEY (`agreementId`) REFERENCES `PROPERTY_AGREEMENT` (`agreementId`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
