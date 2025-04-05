
import React, { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import JobCard from '@/components/JobCard';
import { Search, Sliders } from 'lucide-react';

// Mock job listings data
const jobListings = [
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
  {
    id: '4',
    title: 'UX/UI Design Intern',
    company: 'Creative Studios',
    location: 'San Francisco, CA',
    type: 'Internship' as const,
    deadline: 'April 30, 2025',
  },
  {
    id: '5',
    title: 'Front-end Developer',
    company: 'WebTech',
    location: 'Austin, TX',
    type: 'Full-time' as const,
    deadline: 'May 5, 2025',
  },
  {
    id: '6',
    title: 'Research Assistant',
    company: 'Research Labs',
    location: 'Boston, MA',
    type: 'Part-time' as const,
    deadline: 'April 22, 2025',
  },
  {
    id: '7',
    title: 'Customer Success Manager',
    company: 'SaaS Solutions',
    location: 'Seattle, WA',
    type: 'Full-time' as const,
    deadline: 'May 10, 2025',
  },
  {
    id: '8',
    title: 'Finance Intern',
    company: 'Investment Group',
    location: 'New York, NY',
    type: 'Internship' as const,
    deadline: 'April 18, 2025',
  },
];

const JobsPage = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const [jobType, setJobType] = useState<string>('all');
  const [location, setLocation] = useState<string>('all');
  
  // Filter jobs based on search and filters
  const filteredJobs = jobListings.filter((job) => {
    // Search term filter
    const matchesSearch = 
      job.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
      job.company.toLowerCase().includes(searchTerm.toLowerCase());
    
    // Job type filter
    const matchesType = jobType === 'all' || job.type === jobType;
    
    // Location filter
    const matchesLocation = 
      location === 'all' || 
      job.location.toLowerCase().includes(location.toLowerCase());
    
    return matchesSearch && matchesType && matchesLocation;
  });

  // Reset all filters
  const resetFilters = () => {
    setSearchTerm('');
    setJobType('all');
    setLocation('all');
  };

  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow py-12 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h1 className="text-3xl font-bold mb-4">Find Your Perfect Opportunity</h1>
            <p className="text-muted-foreground max-w-2xl mx-auto">
              Browse through our curated list of internships and job opportunities from top companies.
            </p>
          </div>
          
          <div className="bg-white p-6 rounded-lg shadow-sm mb-8">
            <div className="flex flex-col md:flex-row gap-4">
              <div className="relative flex-grow">
                <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground h-4 w-4" />
                <Input
                  placeholder="Search jobs, companies..."
                  className="pl-10"
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                />
              </div>
              
              <div className="flex flex-col sm:flex-row gap-4">
                <Select value={jobType} onValueChange={setJobType}>
                  <SelectTrigger className="w-full sm:w-[180px]">
                    <SelectValue placeholder="Job Type" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">All Types</SelectItem>
                    <SelectItem value="Internship">Internship</SelectItem>
                    <SelectItem value="Full-time">Full-time</SelectItem>
                    <SelectItem value="Part-time">Part-time</SelectItem>
                  </SelectContent>
                </Select>
                
                <Select value={location} onValueChange={setLocation}>
                  <SelectTrigger className="w-full sm:w-[180px]">
                    <SelectValue placeholder="Location" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">All Locations</SelectItem>
                    <SelectItem value="Remote">Remote</SelectItem>
                    <SelectItem value="New York">New York</SelectItem>
                    <SelectItem value="San Francisco">San Francisco</SelectItem>
                    <SelectItem value="Chicago">Chicago</SelectItem>
                    <SelectItem value="Boston">Boston</SelectItem>
                    <SelectItem value="Austin">Austin</SelectItem>
                    <SelectItem value="Seattle">Seattle</SelectItem>
                  </SelectContent>
                </Select>
                
                <Button variant="outline" size="icon" onClick={resetFilters} title="Reset filters">
                  <Sliders className="h-4 w-4" />
                </Button>
              </div>
            </div>
            
            {/* Active filters */}
            {(searchTerm || jobType !== 'all' || location !== 'all') && (
              <div className="flex flex-wrap gap-2 mt-4">
                <div className="text-sm text-muted-foreground mr-2 mt-1">Active filters:</div>
                
                {searchTerm && (
                  <Badge variant="secondary" className="rounded-full">
                    Search: {searchTerm}
                    <button 
                      className="ml-1 text-xs"
                      onClick={() => setSearchTerm('')}
                    >
                      ×
                    </button>
                  </Badge>
                )}
                
                {jobType !== 'all' && (
                  <Badge variant="secondary" className="rounded-full">
                    Type: {jobType}
                    <button 
                      className="ml-1 text-xs"
                      onClick={() => setJobType('all')}
                    >
                      ×
                    </button>
                  </Badge>
                )}
                
                {location !== 'all' && (
                  <Badge variant="secondary" className="rounded-full">
                    Location: {location}
                    <button 
                      className="ml-1 text-xs"
                      onClick={() => setLocation('all')}
                    >
                      ×
                    </button>
                  </Badge>
                )}
              </div>
            )}
          </div>
          
          <div className="mb-6">
            <h2 className="text-lg font-medium">
              {filteredJobs.length === 0 
                ? 'No jobs found' 
                : `${filteredJobs.length} ${filteredJobs.length === 1 ? 'job' : 'jobs'} found`}
            </h2>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredJobs.map((job) => (
              <JobCard key={job.id} {...job} />
            ))}
          </div>
          
          {filteredJobs.length === 0 && (
            <div className="text-center py-12">
              <h3 className="text-lg font-medium mb-2">No matching jobs found</h3>
              <p className="text-muted-foreground">Try adjusting your search or filters to find what you're looking for.</p>
              <Button variant="outline" className="mt-4" onClick={resetFilters}>
                Reset Filters
              </Button>
            </div>
          )}
        </div>
      </main>
      
      <Footer />
    </div>
  );
};

export default JobsPage;
