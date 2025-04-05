import React, { createContext, useState, useContext, useEffect, ReactNode } from 'react';
import { useToast } from '@/hooks/use-toast';

export type UserRole = 'student' | 'company' | 'admin';

interface User {
  id: string;
  name: string;
  email: string;
  role: UserRole;
  bio?: string;
  skills?: string[];
  education?: string;
  experience?: string;
  phone?: string;
  photoUrl?: string;
}

interface AuthContextType {
  user: User | null;
  isAuthenticated: boolean;
  userRole: UserRole | null;
  login: (email: string, password: string, role: UserRole) => Promise<boolean>;
  register: (userData: RegisterData, role: UserRole) => Promise<boolean>;
  logout: () => void;
  checkRole: (role: UserRole) => boolean;
  updateProfile: (profileData: Partial<User>) => Promise<boolean>;
  updateProfilePhoto: (photoBlob: Blob) => Promise<boolean>;
}

export interface RegisterData {
  name: string;
  email: string;
  password: string;
  [key: string]: string | undefined;
}

const mockUsers = [
  {
    id: '1',
    name: 'John Student',
    email: 'student@example.com',
    password: 'password',
    role: 'student' as UserRole,
    bio: 'Computer Science student with passion for web development.',
    skills: ['JavaScript', 'React', 'Node.js'],
    education: 'B.Tech Computer Science, XYZ University',
    experience: 'Intern at TechCorp (Summer 2024)',
    phone: '123-456-7890',
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
    const emailExists = mockUsers.some((u) => u.email === userData.email);
    
    if (emailExists) {
      toast({
        title: 'Registration failed',
        description: 'Email is already in use.',
        variant: 'destructive',
      });
      return false;
    }

    toast({
      title: 'Registration successful',
      description: role === 'company' 
        ? 'Your company account is pending approval by admin.'
        : 'Your account has been created successfully.',
    });
    
    return true;
  };

  const updateProfile = async (profileData: Partial<User>): Promise<boolean> => {
    if (!user) {
      toast({
        title: 'Update failed',
        description: 'You must be logged in to update your profile.',
        variant: 'destructive',
      });
      return false;
    }

    const updatedUser = { ...user, ...profileData };
    setUser(updatedUser);
    
    localStorage.setItem('user', JSON.stringify(updatedUser));
    
    toast({
      title: 'Profile updated',
      description: 'Your profile has been updated successfully.',
    });
    
    return true;
  };

  const updateProfilePhoto = async (photoBlob: Blob): Promise<boolean> => {
    if (!user) {
      toast({
        title: 'Update failed',
        description: 'You must be logged in to update your profile photo.',
        variant: 'destructive',
      });
      return false;
    }
    
    try {
      const photoUrl = URL.createObjectURL(photoBlob);
      
      const updatedUser = { ...user, photoUrl };
      setUser(updatedUser);
      
      localStorage.setItem('user', JSON.stringify(updatedUser));
      
      toast({
        title: 'Photo updated',
        description: 'Your profile photo has been updated successfully.',
      });
      
      return true;
    } catch (error) {
      console.error('Error updating profile photo:', error);
      
      toast({
        title: 'Update failed',
        description: 'There was an error updating your profile photo.',
        variant: 'destructive',
      });
      
      return false;
    }
  };

  const logout = () => {
    setUser(null);
    localStorage.removeItem('user');
    
    toast({
      title: 'Logged out',
      description: 'You have been logged out successfully.',
    });
  };
  
  const checkRole = (role: UserRole): boolean => {
    return user?.role === role;
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
        checkRole,
        updateProfile,
        updateProfilePhoto,
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
