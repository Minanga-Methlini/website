-- Insert sample data for Medicare Appointment Booking System

USE medicare_system;

-- Insert departments
INSERT INTO departments (name, description) VALUES
('Cardiology', 'Heart and cardiovascular system specialists'),
('Pediatrics', 'Medical care for infants, children, and adolescents'),
('Orthopedics', 'Musculoskeletal system specialists'),
('Dermatology', 'Skin, hair, and nail specialists'),
('Neurology', 'Nervous system specialists');

-- Insert admin user
INSERT INTO users (username, email, password, role, first_name, last_name, phone) VALUES
('admin', 'admin@medicare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'System', 'Administrator', '1234567890');

-- Insert sample doctors
INSERT INTO users (username, email, password, role, first_name, last_name, phone) VALUES
('dr_smith', 'dr.smith@medicare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', 'John', 'Smith', '1234567891'),
('dr_johnson', 'dr.johnson@medicare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', 'Sarah', 'Johnson', '1234567892'),
('dr_brown', 'dr.brown@medicare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', 'Michael', 'Brown', '1234567893');

-- Insert sample patients
INSERT INTO users (username, email, password, role, first_name, last_name, phone) VALUES
('patient1', 'patient1@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', 'Alice', 'Wilson', '1234567894'),
('patient2', 'patient2@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', 'Bob', 'Davis', '1234567895');

-- Insert doctor profiles
INSERT INTO doctor_profiles (user_id, department_id, specialization, bio) VALUES
(2, 1, 'Interventional Cardiology', 'Experienced cardiologist with 15 years of practice'),
(3, 2, 'General Pediatrics', 'Specialized in child healthcare and development'),
(4, 3, 'Sports Medicine', 'Expert in sports injuries and rehabilitation');

-- Insert patient profiles
INSERT INTO patient_profiles (user_id, date_of_birth, gender, address) VALUES
(5, '1985-06-15', 'female', '123 Main St, City, State'),
(6, '1990-03-22', 'male', '456 Oak Ave, City, State');
