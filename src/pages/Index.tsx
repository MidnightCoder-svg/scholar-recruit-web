
import React from 'react';
import { Link } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import JobCard from '@/components/JobCard';
import { useAuth } from '@/hooks/useAuth';
import { Briefcase, Users, User } from 'lucide-react';

// Mock data for featured jobs
const featuredJobs = [
  {
    id: '1',
    title: 'Software Engineering Intern',
    company: 'TechSolutions Inc.',
    location: 'Remote',
    type: 'Internship' as const,
    deadline: 'April 20, 2025',
  },
  {
    id: '2',
    title: 'Data Analyst',
    company: 'Analytics Pro',
    location: 'New York, NY',
    type: 'Full-time' as const,
    deadline: 'April 25, 2025',
  },
  {
    id: '3',
    title: 'Marketing Associate',
    company: 'Global Brands',
    location: 'Chicago, IL',
    type: 'Full-time' as const,
    deadline: 'April 15, 2025',
  },
];

const Index = () => {
  const { isAuthenticated, userRole } = useAuth();

  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow">
        {/* Hero Section */}
        <section className="hero-gradient text-white py-20 px-4">
          <div className="container mx-auto text-center">
            <h1 className="text-4xl font-bold mb-6 md:text-5xl lg:text-6xl">
              Your Gateway to Professional Success
            </h1>
            <p className="text-lg mb-8 max-w-2xl mx-auto text-recruit-100">
              Connect with top companies, apply for internships and job opportunities, and launch your career with ScholarRecruit.
            </p>
            <div className="flex flex-col sm:flex-row justify-center gap-4">
              {isAuthenticated ? (
                <Button size="lg" asChild>
                  <Link to={`/${userRole}/dashboard`}>Go to Dashboard</Link>
                </Button>
              ) : (
                <>
                  <Button size="lg" asChild>
                    <Link to="/register">Sign Up Now</Link>
                  </Button>
                  <Button variant="outline" size="lg" className="text-white border-white hover:bg-white/10" asChild>
                    <Link to="/jobs">Browse Opportunities</Link>
                  </Button>
                </>
              )}
            </div>
          </div>
        </section>
        
        {/* Stats Section */}
        <section className="py-16 bg-white">
          <div className="container mx-auto px-4">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              <Card>
                <CardContent className="p-8 flex flex-col items-center text-center">
                  <div className="h-12 w-12 bg-recruit-100 rounded-full flex items-center justify-center mb-4">
                    <Briefcase className="h-6 w-6 text-recruit-600" />
                  </div>
                  <h3 className="text-2xl font-bold mb-1">500+</h3>
                  <p className="text-muted-foreground">Job Opportunities</p>
                </CardContent>
              </Card>
              
              <Card>
                <CardContent className="p-8 flex flex-col items-center text-center">
                  <div className="h-12 w-12 bg-recruit-100 rounded-full flex items-center justify-center mb-4">
                    <Users className="h-6 w-6 text-recruit-600" />
                  </div>
                  <h3 className="text-2xl font-bold mb-1">200+</h3>
                  <p className="text-muted-foreground">Partner Companies</p>
                </CardContent>
              </Card>
              
              <Card>
                <CardContent className="p-8 flex flex-col items-center text-center">
                  <div className="h-12 w-12 bg-recruit-100 rounded-full flex items-center justify-center mb-4">
                    <User className="h-6 w-6 text-recruit-600" />
                  </div>
                  <h3 className="text-2xl font-bold mb-1">1000+</h3>
                  <p className="text-muted-foreground">Placed Students</p>
                </CardContent>
              </Card>
            </div>
          </div>
        </section>
        
        {/* Featured Jobs Section */}
        <section className="py-16 bg-gray-50">
          <div className="container mx-auto px-4">
            <div className="text-center mb-12">
              <h2 className="text-3xl font-bold mb-4">Featured Opportunities</h2>
              <p className="text-muted-foreground max-w-2xl mx-auto">
                Discover the latest internships and job opportunities from top companies.
              </p>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {featuredJobs.map((job) => (
                <JobCard key={job.id} {...job} />
              ))}
            </div>
            
            <div className="text-center mt-12">
              <Button size="lg" asChild>
                <Link to="/jobs">View All Jobs</Link>
              </Button>
            </div>
          </div>
        </section>
        
        {/* How It Works Section */}
        <section className="py-16 bg-white">
          <div className="container mx-auto px-4">
            <div className="text-center mb-12">
              <h2 className="text-3xl font-bold mb-4">How It Works</h2>
              <p className="text-muted-foreground max-w-2xl mx-auto">
                Our platform makes it easy to connect students with companies.
              </p>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              <div className="text-center">
                <div className="inline-flex h-12 w-12 rounded-full bg-recruit-100 items-center justify-center mb-4">
                  <span className="text-lg font-bold text-recruit-600">1</span>
                </div>
                <h3 className="text-xl font-semibold mb-2">Create Your Profile</h3>
                <p className="text-muted-foreground">
                  Register and build your professional profile with academic details, skills, and resume.
                </p>
              </div>
              
              <div className="text-center">
                <div className="inline-flex h-12 w-12 rounded-full bg-recruit-100 items-center justify-center mb-4">
                  <span className="text-lg font-bold text-recruit-600">2</span>
                </div>
                <h3 className="text-xl font-semibold mb-2">Discover Opportunities</h3>
                <p className="text-muted-foreground">
                  Browse through job and internship listings from top companies.
                </p>
              </div>
              
              <div className="text-center">
                <div className="inline-flex h-12 w-12 rounded-full bg-recruit-100 items-center justify-center mb-4">
                  <span className="text-lg font-bold text-recruit-600">3</span>
                </div>
                <h3 className="text-xl font-semibold mb-2">Apply & Get Hired</h3>
                <p className="text-muted-foreground">
                  Apply to positions that match your skills and start your professional journey.
                </p>
              </div>
            </div>
          </div>
        </section>
        
        {/* CTA Section */}
        <section className="py-16 bg-recruit-800 text-white">
          <div className="container mx-auto px-4 text-center">
            <h2 className="text-3xl font-bold mb-6">Ready to Launch Your Career?</h2>
            <p className="text-lg mb-8 max-w-2xl mx-auto text-recruit-100">
              Join thousands of students who have found their dream roles through ScholarRecruit.
            </p>
            <div className="flex flex-col sm:flex-row justify-center gap-4">
              <Button size="lg" variant="default" className="bg-white text-recruit-800 hover:bg-recruit-100" asChild>
                <Link to="/register">Sign Up as Student</Link>
              </Button>
              <Button variant="outline" size="lg" className="text-white border-white hover:bg-white/10" asChild>
                <Link to="/company/register">Register Company</Link>
              </Button>
            </div>
          </div>
        </section>
      </main>
      
      <Footer />
    </div>
  );
};

export default Index;
