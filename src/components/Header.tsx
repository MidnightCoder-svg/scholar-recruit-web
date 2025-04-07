
import React from 'react';
import { Link } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { useAuth } from '@/hooks/useAuth';

const Header = () => {
  const { isAuthenticated, userRole, logout } = useAuth();

  return (
    <header className="bg-white border-b border-gray-200">
      <div className="container mx-auto px-4">
        <div className="flex justify-between items-center h-16">
          <div className="flex items-center">
            <Link to="/" className="text-xl font-bold text-recruit-600">
              ScholarRecruit
            </Link>
            
            <nav className="hidden md:flex ml-10 space-x-6">
              <Link to="/jobs" className="text-gray-600 hover:text-recruit-600">
                Jobs
              </Link>
              <Link to="/about" className="text-gray-600 hover:text-recruit-600">
                About
              </Link>
              <Link to="/contact" className="text-gray-600 hover:text-recruit-600">
                Contact
              </Link>
            </nav>
          </div>
          
          <div className="flex items-center">
            {isAuthenticated ? (
              <>
                <Link 
                  to={`/${userRole}/dashboard`} 
                  className="text-gray-600 hover:text-recruit-600 mr-6"
                >
                  Dashboard
                </Link>
                <Button variant="outline" onClick={logout}>
                  Logout
                </Button>
              </>
            ) : (
              <>
                <Link 
                  to="/login" 
                  className="text-gray-600 hover:text-recruit-600 mr-6"
                >
                  Login
                </Link>
                <Button asChild>
                  <Link to="/register">
                    Register
                  </Link>
                </Button>
              </>
            )}
          </div>
        </div>
      </div>
    </header>
  );
};

export default Header;
