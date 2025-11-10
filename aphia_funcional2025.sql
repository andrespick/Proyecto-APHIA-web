-- DROP DATABASE IF EXISTS Aphia_P;
CREATE DATABASE Aphia_P;
USE Aphia_P;

-- Unified person table (Owners, Tenants, Co-debtors)
CREATE TABLE PERSON (
  personId INT PRIMARY KEY,
  personCategory VARCHAR(50) COMMENT 'Owner, Tenant, Co-Debtor',
  fullName VARCHAR(255),
  documentCategory VARCHAR(10) COMMENT 'ID, TI, CE, PAS, etc.',
  documentIdentifier VARCHAR(50),
  address VARCHAR(255),
  phoneNumber VARCHAR(50),
  phonePrefix VARCHAR(100) COMMENT 'e.g. +57, +1, +34, etc.',
  emailAddress VARCHAR(255)
);

-- Separate bank account table
CREATE TABLE BANK_ACCOUNT (
  accountId INT PRIMARY KEY,
  personId INT NOT NULL,
  accountIdentifier VARCHAR(50),
  accountCategory VARCHAR(50) COMMENT 'Savings, Checking, etc.',
  financialInstitution VARCHAR(100),
  FOREIGN KEY (personId) REFERENCES PERSON(personId)
);

-- Properties associated with owners
CREATE TABLE PROPERTY (
  propertyId INT PRIMARY KEY,
  address VARCHAR(255),
  city VARCHAR(255),
  registrationIdentifier VARCHAR(255),
  rentalValue DECIMAL(12, 2),
  utilityContractIdentifiers VARCHAR(255) COMMENT 'Utility service contract identifiers',
  occupancyState VARCHAR(255),
  propertyType VARCHAR(50) COMMENT 'Tipo de inmueble: Apartamento, Casa, Oficina, etc.',
  ownerId INT NOT NULL,
  FOREIGN KEY (ownerId) REFERENCES PERSON(personId)
);

-- Contracts (includes lease, brokerage, power of attorney, etc.)
CREATE TABLE PROPERTY_AGREEMENT (
  agreementId INT PRIMARY KEY,
  agreementCategory VARCHAR(50) COMMENT 'Lease, Brokerage, PowerOfAttorney',
  agreementContent LONGTEXT COMMENT 'Full agreement content',
  startDate DATE,
  endDate DATE,
  conditions TEXT,
  propertyId INT,
  associatedPersonId INT NOT NULL COMMENT 'Tenant or Owner depending on agreement category',
  FOREIGN KEY (propertyId) REFERENCES PROPERTY(propertyId),
  FOREIGN KEY (associatedPersonId) REFERENCES PERSON(personId)
);

-- Contract-Co-debtor relation
CREATE TABLE AGREEMENT_CO_DEBTOR (
  agreementId INT NOT NULL,
  coDebtorId INT NOT NULL,
  PRIMARY KEY (agreementId, coDebtorId),
  FOREIGN KEY (agreementId) REFERENCES PROPERTY_AGREEMENT(agreementId),
  FOREIGN KEY (coDebtorId) REFERENCES PERSON(personId)
);

-- Financial records associated with agreements
CREATE TABLE FINANCIAL_RECORD (
  recordId INT PRIMARY KEY,
  recordCategory VARCHAR(255) COMMENT 'Rent payment, administration fee, property repair, etc.',
  amount DECIMAL(12,2),
  recordDate DATE,
  paymentState VARCHAR(255) COMMENT 'Pending, Overdue, Paid, etc.',
  agreementId INT NOT NULL,
  FOREIGN KEY (agreementId) REFERENCES PROPERTY_AGREEMENT(agreementId)
);

-- Documents related to persons and properties
CREATE TABLE FILE_RECORD (
  fileId INT PRIMARY KEY,
  fileName VARCHAR(255),
  fileCategory VARCHAR(255) COMMENT 'Agreement, ID, Deed, etc.',
  filePath VARCHAR(255) COMMENT 'Digital file location',
  uploadDate DATE,
  personId INT,
  propertyId INT,
  FOREIGN KEY (personId) REFERENCES PERSON(personId),
  FOREIGN KEY (propertyId) REFERENCES PROPERTY(propertyId)
);

-- System users
CREATE TABLE USER_ACCOUNT (
  accountId INT PRIMARY KEY,
  userName VARCHAR(255),
  documentCategory VARCHAR(10),
  documentIdentifier VARCHAR(50),
  emailAddress VARCHAR(255),
  hashedPassword VARCHAR(255) COMMENT 'Hashed password',
  userCategory VARCHAR(50) COMMENT 'Administrator, Staff, Advisor'
);

-- Usuario de prueba: Administrador del sistema
INSERT INTO USER_ACCOUNT (
  accountId,
  userName,
  documentCategory,
  documentIdentifier,
  emailAddress,
  hashedPassword,
  userCategory
) VALUES (
  1,
  'testsysadmin',
  'ID',
  '98765432',
  'testadmin@example.com',
  'password123',      -- Para pruebas con contraseña en texto
  'System Administrator'
);
INSERT INTO USER_ACCOUNT (
  accountId,
  userName,
  documentCategory,
  documentIdentifier,
  emailAddress,
  hashedPassword,
  userCategory
) VALUES (
  2,
  'testadmin',
  'ID',
  '98765432',
  'testadmin@example.com',
  'password123',      -- Para pruebas con contraseña en texto
  'administrator'
);
INSERT INTO USER_ACCOUNT (
  accountId,
  userName,
  documentCategory,
  documentIdentifier,
  emailAddress,
  hashedPassword,
  userCategory
) VALUES (
  3,
  'testAdvisor',
  'ID',
  '98765432',
  'testadmin@example.com',
  'password123',      -- Para pruebas con contraseña en texto
  'Advisor'
);
  
