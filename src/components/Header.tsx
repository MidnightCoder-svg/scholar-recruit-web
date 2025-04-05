
import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { useAuth } from '@/hooks/useAuth';

const Header = () => {
  const location = useLocation();
  const { isAuthenticated, userRole, logout } = useAuth();

  return (
    <header className="border-b border-border">
      <div className="container flex items-center justify-between h-16 px-4 mx-auto sm:px-6">
        <Link to="/" className="text-2xl font-bold text-recruit-800">
          ScholarRecruit
        </Link>
        
        <nav className="hidden md:flex items-center space-x-6">
          <Link 
            to="/" 
            className={`text-sm font-medium transition-colors hover:text-primary ${
              location.pathname === '/' ? 'text-primary' : 'text-muted-foreground'
            }`}
          >
            Home
          </Link>
          <Link 
            to="/jobs" 
            className={`text-sm font-medium transition-colors hover:text-primary ${
              location.pathname === '/jobs' ? 'text-primary' : 'text-muted-foreground'
            }`}
          >
            Jobs
          </Link>
          <Link 
            to="/about" 
            className={`text-sm font-medium transition-colors hover:text-primary ${
              location.pathname === '/about' ? 'text-primary' : 'text-muted-foreground'
            }`}
          >
            About
          </Link>
          <Link 
            to="/contact" 
            className={`text-sm font-medium transition-colors hover:text-primary ${
              location.pathname === '/contact' ? 'text-primary' : 'text-muted-foreground'
            }`}
          >
            Contact
          </Link>
        </nav>

        <div className="flex items-center space-x-4">
          {isAuthenticated ? (
            <>
              <Button 
                variant="ghost" 
                size="sm"
                asChild
              >
                <Link to={`/${userRole}/dashboard`}>Dashboard</Link>
              </Button>
              <Button 
                variant="default" 
                size="sm"
                onClick={logout}
              >
                Logout
              </Button>
            </>
          ) : (
            <>
              <Button 
                variant="ghost" 
                size="sm"
                asChild
              >
                <Link to="/login">Login</Link>
              </Button>
              <Button 
                variant="default" 
                size="sm"
                asChild
              >
                <Link to="/register">Register</Link>
              </Button>
            </>
          )}
        </div>
      </div>
    </header>
  );
};

export default Header;
