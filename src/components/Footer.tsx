
import React from 'react';
import { Link } from 'react-router-dom';

const Footer = () => {
  return (
    <footer className="bg-recruit-800 text-white py-12">
      <div className="container mx-auto px-4 sm:px-6">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <h3 className="text-xl font-bold mb-4">ScholarRecruit</h3>
            <p className="text-recruit-100 text-sm">
              Connecting students with their dream companies for internships and placements.
            </p>
          </div>
          <div>
            <h4 className="font-semibold mb-4">Quick Links</h4>
            <ul className="space-y-2 text-sm">
              <li><Link to="/" className="hover:text-recruit-200 transition-colors">Home</Link></li>
              <li><Link to="/jobs" className="hover:text-recruit-200 transition-colors">Browse Jobs</Link></li>
              <li><Link to="/about" className="hover:text-recruit-200 transition-colors">About Us</Link></li>
              <li><Link to="/contact" className="hover:text-recruit-200 transition-colors">Contact</Link></li>
            </ul>
          </div>
          <div>
            <h4 className="font-semibold mb-4">For Students</h4>
            <ul className="space-y-2 text-sm">
              <li><Link to="/register" className="hover:text-recruit-200 transition-colors">Register</Link></li>
              <li><Link to="/login" className="hover:text-recruit-200 transition-colors">Login</Link></li>
              <li><Link to="/resources" className="hover:text-recruit-200 transition-colors">Resources</Link></li>
              <li><Link to="/faqs" className="hover:text-recruit-200 transition-colors">FAQs</Link></li>
            </ul>
          </div>
          <div>
            <h4 className="font-semibold mb-4">For Companies</h4>
            <ul className="space-y-2 text-sm">
              <li><Link to="/company/register" className="hover:text-recruit-200 transition-colors">Register Company</Link></li>
              <li><Link to="/company/login" className="hover:text-recruit-200 transition-colors">Company Login</Link></li>
              <li><Link to="/post-job" className="hover:text-recruit-200 transition-colors">Post a Job</Link></li>
              <li><Link to="/terms" className="hover:text-recruit-200 transition-colors">Terms & Conditions</Link></li>
            </ul>
          </div>
        </div>
        <div className="border-t border-recruit-700 mt-8 pt-8 text-center text-sm text-recruit-300">
          <p>&copy; {new Date().getFullYear()} ScholarRecruit. All rights reserved.</p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
