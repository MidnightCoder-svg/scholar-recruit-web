import React, { createContext, useState, useContext, useEffect, ReactNode } from 'react';
import { useToast } from '@/components/ui/use-toast';

export type UserRole = 'student' | 'company' | 'admin';

interface User {
  id: string;
  name: string;
  email: string;
  role: UserRole;
}

interface AuthContextType {
  user: User | null;
  isAuthenticated: boolean;
  userRole: UserRole | null;
  login: (email: string, password: string, role: UserRole) => Promise<boolean>;
  register: (userData: RegisterData, role: UserRole) => Promise<boolean>;
  logout: () => void;
}

export interface RegisterData {
  name: string;
  email: string;
  password: string;
  [key: string]: string | undefined;
}

// Mock users for demonstration
const mockUsers = [
  {
    id: '1',
    name: 'John Student',
    email: 'student@example.com',
    password: 'password',
    role: 'student' as UserRole,
  },
  {
    id: '2',
    name: 'Tech Company',
    email: 'company@example.com',
    password: 'password',
    role: 'company' as UserRole,
  },
  {
    id: '3',
    name: 'Admin User',
    email: 'admin@example.com',
    password: 'password',
    role: 'admin' as UserRole,
  },
];

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider = ({ children }: { children: ReactNode }) => {
  const [user, setUser] = useState<User | null>(null);
  const { toast } = useToast();
  
  // Check for saved auth on component mount
  useEffect(() => {
    const savedUser = localStorage.getItem('user');
    if (savedUser) {
      try {
        setUser(JSON.parse(savedUser));
      } catch (error) {
        console.error('Failed to parse saved user:', error);
        localStorage.removeItem('user');
      }
    }
  }, []);

  const login = async (email: string, password: string, role: UserRole): Promise<boolean> => {
    // In a real application, this would be an API request
    const user = mockUsers.find(
      (u) => u.email === email && u.password === password && u.role === role
    );

    if (user) {
      const { password, ...userWithoutPassword } = user;
      setUser(userWithoutPassword);
      localStorage.setItem('user', JSON.stringify(userWithoutPassword));
      
      toast({
        title: 'Login successful',
        description: `Welcome back, ${userWithoutPassword.name}!`,
      });
      
      return true;
    }
    
    toast({
      title: 'Login failed',
      description: 'Invalid email or password.',
      variant: 'destructive',
    });
    
    return false;
  };

  const register = async (userData: RegisterData, role: UserRole): Promise<boolean> => {
    // In a real app, this would be an API request
    // Check if email is already in use
    const emailExists = mockUsers.some((u) => u.email === userData.email);
    
    if (emailExists) {
      toast({
        title: 'Registration failed',
        description: 'Email is already in use.',
        variant: 'destructive',
      });
      return false;
    }

    // Registration successful
    toast({
      title: 'Registration successful',
      description: role === 'company' 
        ? 'Your company account is pending approval by admin.'
        : 'Your account has been created successfully.',
    });
    
    return true;
  };

  const logout = () => {
    setUser(null);
    localStorage.removeItem('user');
    
    toast({
      title: 'Logged out',
      description: 'You have been logged out successfully.',
    });
  };

  return (
    <AuthContext.Provider
      value={{
        user,
        isAuthenticated: !!user,
        userRole: user?.role || null,
        login,
        register,
        logout,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};
