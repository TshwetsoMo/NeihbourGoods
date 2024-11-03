-- Create the database
CREATE DATABASE neighbourgoods_db;
USE neighbourgoods_db;

-- Create the Users table
CREATE TABLE Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL,
    Address VARCHAR(255),
    UserType ENUM('Donor', 'Recipient', 'Trader', 'Admin') NOT NULL,
    Rating FLOAT DEFAULT 0,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the Items table
CREATE TABLE Items (
    ItemID INT AUTO_INCREMENT PRIMARY KEY,
    OwnerID INT NOT NULL,
    ItemType ENUM('Food', 'Good') NOT NULL,
    Description TEXT NOT NULL,
    Quantity INT NOT NULL,
    ExpirationDate DATE,
    ImagePath VARCHAR(255),
    Status ENUM('Available', 'Pending', 'Exchanged') DEFAULT 'Available',
    Category VARCHAR(100),
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (OwnerID) REFERENCES Users(UserID)
);

-- Create the Offers table
CREATE TABLE Offers (
    OfferID INT AUTO_INCREMENT PRIMARY KEY,
    ItemOfferedID INT NOT NULL,
    OfferedByUserID INT NOT NULL,
    ItemRequestedID INT NOT NULL,
    RequestedFromUserID INT NOT NULL,
    OfferDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('Pending', 'Accepted', 'Declined') DEFAULT 'Pending',
    FOREIGN KEY (ItemOfferedID) REFERENCES Items(ItemID),
    FOREIGN KEY (OfferedByUserID) REFERENCES Users(UserID),
    FOREIGN KEY (ItemRequestedID) REFERENCES Items(ItemID),
    FOREIGN KEY (RequestedFromUserID) REFERENCES Users(UserID)
);

-- Create the Requests table
CREATE TABLE Requests (
    RequestID INT AUTO_INCREMENT PRIMARY KEY,
    ItemRequestedID INT NOT NULL,
    RequestedByUserID INT NOT NULL,
    RequestDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('Pending', 'Fulfilled', 'Cancelled') DEFAULT 'Pending',
    FOREIGN KEY (ItemRequestedID) REFERENCES Items(ItemID),
    FOREIGN KEY (RequestedByUserID) REFERENCES Users(UserID)
);

-- Create the Messages table
CREATE TABLE Messages (
    MessageID INT AUTO_INCREMENT PRIMARY KEY,
    SenderID INT NOT NULL,
    ReceiverID INT NOT NULL,
    Content TEXT NOT NULL,
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (SenderID) REFERENCES Users(UserID),
    FOREIGN KEY (ReceiverID) REFERENCES Users(UserID)
);

-- Create the Feedback table
CREATE TABLE Feedback (
    FeedbackID INT AUTO_INCREMENT PRIMARY KEY,
    GivenByUserID INT NOT NULL,
    ReceivedByUserID INT NOT NULL,
    Rating INT CHECK (Rating BETWEEN 1 AND 5),
    Comments TEXT,
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (GivenByUserID) REFERENCES Users(UserID),
    FOREIGN KEY (ReceivedByUserID) REFERENCES Users(UserID)
);

-- Indexes for performance
CREATE INDEX idx_ownerid ON Items(OwnerID);
CREATE INDEX idx_offeredbyuserid ON Offers(OfferedByUserID);
CREATE INDEX idx_requestedbyuserid ON Requests(RequestedByUserID);