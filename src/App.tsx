
import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Index from './pages/Index';
import Login from './pages/Login';
import Register from './pages/Register';
import About from './pages/About';
import Contact from './pages/Contact';
import JobsPage from './pages/JobsPage';
import JobDetail from './pages/JobDetail';
import StudentProfile from './pages/StudentProfile';
import CompanyProfile from './pages/CompanyProfile';
import StudentDashboard from './pages/dashboards/StudentDashboard';
import CompanyDashboard from './pages/dashboards/CompanyDashboard';
import AdminDashboard from './pages/dashboards/AdminDashboard';
import NotFound from './pages/NotFound';

const App = () => {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<Index />} />
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />
        <Route path="/about" element={<About />} />
        <Route path="/contact" element={<Contact />} />
        <Route path="/jobs" element={<JobsPage />} />
        <Route path="/jobs/:id" element={<JobDetail />} />
        <Route path="/student/profile" element={<StudentProfile />} />
        <Route path="/company/profile" element={<CompanyProfile />} />
        <Route path="/student/dashboard" element={<StudentDashboard />} />
        <Route path="/company/dashboard" element={<CompanyDashboard />} />
        <Route path="/admin/dashboard" element={<AdminDashboard />} />
        <Route path="*" element={<NotFound />} />
      </Routes>
    </Router>
  );
};

export default App;
