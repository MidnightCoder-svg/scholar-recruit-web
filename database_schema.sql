
-- Create database
CREATE DATABASE IF NOT EXISTS scholar_recruit;
USE scholar_recruit;

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('student', 'company', 'admin') NOT NULL,
  bio TEXT,
  skills TEXT,
  education TEXT,
  experience TEXT,
  phone VARCHAR(20),
  photo_url VARCHAR(255),
  website VARCHAR(255),
  location VARCHAR(255),
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Jobs table
CREATE TABLE IF NOT EXISTS jobs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  location VARCHAR(255) NOT NULL,
  type ENUM('Full-time', 'Part-time', 'Internship', 'Contract') NOT NULL,
  salary VARCHAR(100),
  deadline DATE NOT NULL,
  qualifications TEXT,
  skills TEXT,
  duration VARCHAR(100),
  posted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (company_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Applications table
CREATE TABLE IF NOT EXISTS applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  job_id INT NOT NULL,
  student_id INT NOT NULL,
  status ENUM('pending', 'reviewed', 'accepted', 'rejected') DEFAULT 'pending',
  applied_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);
