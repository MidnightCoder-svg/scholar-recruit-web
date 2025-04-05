
import React from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import { Link } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { 
  Users, Briefcase, Building, CheckCircle, XCircle, 
  ArrowUpDown, Bell, FileText, BarChart
} from 'lucide-react';

// Mock data for pending approvals
const pendingStudents = [
  {
    id: '1',
    name: 'Alex Johnson',
    email: 'alex@example.com',
    college: 'MIT',
    degree: 'B.Tech Computer Science',
    registeredDate: 'March 25, 2025',
  },
  {
    id: '2',
    name: 'Samantha Lee',
    email: 'samantha@example.com',
    college: 'Stanford University',
    degree: 'B.Sc Data Science',
    registeredDate: 'March 24, 2025',
  },
];

const pendingCompanies = [
  {
    id: '1',
    name: 'InnovationTech',
    email: 'hr@innovationtech.com',
    industry: 'Software Development',
    location: 'San Francisco, CA',
    registeredDate: 'March 26, 2025',
  },
  {
    id: '2',
    name: 'Global Finance',
    email: 'careers@globalfinance.com',
    industry: 'Finance',
    location: 'New York, NY',
    registeredDate: 'March 25, 2025',
  },
];

// Mock data for job postings
const jobPostings = [
  {
    id: '1',
    title: 'Software Engineering Intern',
    company: 'TechSolutions Inc.',
    location: 'Remote',
    type: 'Internship',
    postedDate: 'March 15, 2025',
    status: 'Active',
  },
  {
    id: '2',
    title: 'Frontend Developer',
    company: 'WebTech',
    location: 'New York, NY',
    type: 'Full-time',
    postedDate: 'March 10, 2025',
    status: 'Pending Review',
  },
  {
    id: '3',
    title: 'Data Analyst',
    company: 'Analytics Pro',
    location: 'Chicago, IL',
    type: 'Full-time',
    postedDate: 'March 8, 2025',
    status: 'Active',
  },
];

const AdminDashboard = () => {
  const { user } = useAuth();
  
  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow py-12 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="flex justify-between items-center mb-8">
            <h1 className="text-2xl font-bold">Admin Dashboard</h1>
            <div className="flex gap-3">
              <Button asChild>
                <Link to="/admin/announcements">
                  <Bell className="h-4 w-4 mr-2" />
                  Send Announcement
                </Link>
              </Button>
              <Button variant="outline" asChild>
                <Link to="/admin/reports">
                  <FileText className="h-4 w-4 mr-2" />
                  Reports
                </Link>
              </Button>
            </div>
          </div>
          
          {/* Stats Overview */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <Card>
              <CardContent className="flex items-center justify-between pt-6">
                <div>
                  <p className="text-sm text-muted-foreground">Registered Students</p>
                  <p className="text-2xl font-bold">245</p>
                </div>
                <div className="p-2 bg-recruit-100 rounded-full">
                  <Users className="h-6 w-6 text-recruit-600" />
                </div>
              </CardContent>
            </Card>
            
            <Card>
              <CardContent className="flex items-center justify-between pt-6">
                <div>
                  <p className="text-sm text-muted-foreground">Registered Companies</p>
                  <p className="text-2xl font-bold">32</p>
                </div>
                <div className="p-2 bg-recruit-100 rounded-full">
                  <Building className="h-6 w-6 text-recruit-600" />
                </div>
              </CardContent>
            </Card>
            
            <Card>
              <CardContent className="flex items-center justify-between pt-6">
                <div>
                  <p className="text-sm text-muted-foreground">Active Job Postings</p>
                  <p className="text-2xl font-bold">{jobPostings.filter(job => job.status === 'Active').length}</p>
                </div>
                <div className="p-2 bg-recruit-100 rounded-full">
                  <Briefcase className="h-6 w-6 text-recruit-600" />
                </div>
              </CardContent>
            </Card>
            
            <Card>
              <CardContent className="flex items-center justify-between pt-6">
                <div>
                  <p className="text-sm text-muted-foreground">Students Placed</p>
                  <p className="text-2xl font-bold">78</p>
                </div>
                <div className="p-2 bg-recruit-100 rounded-full">
                  <BarChart className="h-6 w-6 text-recruit-600" />
                </div>
              </CardContent>
            </Card>
          </div>
          
          <Tabs defaultValue="approvals">
            <TabsList className="mb-6">
              <TabsTrigger value="approvals">Pending Approvals</TabsTrigger>
              <TabsTrigger value="jobs">Job Postings</TabsTrigger>
              <TabsTrigger value="placements">Placement Statistics</TabsTrigger>
            </TabsList>
            
            <TabsContent value="approvals">
              <div className="space-y-6">
                <Card>
                  <CardHeader>
                    <CardTitle>Pending Student Approvals</CardTitle>
                  </CardHeader>
                  <CardContent>
                    {pendingStudents.length > 0 ? (
                      <div className="space-y-4">
                        {pendingStudents.map((student) => (
                          <div 
                            key={student.id} 
                            className="flex flex-col md:flex-row md:items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition-colors"
                          >
                            <div className="mb-4 md:mb-0">
                              <p className="font-medium">{student.name}</p>
                              <p className="text-sm">{student.email}</p>
                              <div className="flex flex-col sm:flex-row gap-1 sm:gap-4 mt-1">
                                <p className="text-sm text-muted-foreground">
                                  {student.college} • {student.degree}
                                </p>
                                <p className="text-sm text-muted-foreground">
                                  Registered: {student.registeredDate}
                                </p>
                              </div>
                            </div>
                            
                            <div className="flex gap-2">
                              <Button variant="outline" size="sm" className="text-green-600">
                                <CheckCircle className="h-4 w-4 mr-1" />
                                Approve
                              </Button>
                              <Button variant="outline" size="sm" className="text-red-600">
                                <XCircle className="h-4 w-4 mr-1" />
                                Reject
                              </Button>
                              <Button variant="outline" size="sm" asChild>
                                <Link to={`/admin/student/${student.id}`}>
                                  View Details
                                </Link>
                              </Button>
                            </div>
                          </div>
                        ))}
                      </div>
                    ) : (
                      <div className="text-center py-8">
                        <Users className="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                        <h3 className="text-lg font-medium mb-2">No pending student approvals</h3>
                        <p className="text-muted-foreground">
                          All student registrations have been processed.
                        </p>
                      </div>
                    )}
                  </CardContent>
                </Card>
                
                <Card>
                  <CardHeader>
                    <CardTitle>Pending Company Approvals</CardTitle>
                  </CardHeader>
                  <CardContent>
                    {pendingCompanies.length > 0 ? (
                      <div className="space-y-4">
                        {pendingCompanies.map((company) => (
                          <div 
                            key={company.id} 
                            className="flex flex-col md:flex-row md:items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition-colors"
                          >
                            <div className="mb-4 md:mb-0">
                              <p className="font-medium">{company.name}</p>
                              <p className="text-sm">{company.email}</p>
                              <div className="flex flex-col sm:flex-row gap-1 sm:gap-4 mt-1">
                                <p className="text-sm text-muted-foreground">
                                  {company.industry} • {company.location}
                                </p>
                                <p className="text-sm text-muted-foreground">
                                  Registered: {company.registeredDate}
                                </p>
                              </div>
                            </div>
                            
                            <div className="flex gap-2">
                              <Button variant="outline" size="sm" className="text-green-600">
                                <CheckCircle className="h-4 w-4 mr-1" />
                                Approve
                              </Button>
                              <Button variant="outline" size="sm" className="text-red-600">
                                <XCircle className="h-4 w-4 mr-1" />
                                Reject
                              </Button>
                              <Button variant="outline" size="sm" asChild>
                                <Link to={`/admin/company/${company.id}`}>
                                  View Details
                                </Link>
                              </Button>
                            </div>
                          </div>
                        ))}
                      </div>
                    ) : (
                      <div className="text-center py-8">
                        <Building className="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                        <h3 className="text-lg font-medium mb-2">No pending company approvals</h3>
                        <p className="text-muted-foreground">
                          All company registrations have been processed.
                        </p>
                      </div>
                    )}
                  </CardContent>
                </Card>
              </div>
            </TabsContent>
            
            <TabsContent value="jobs">
              <Card>
                <CardHeader className="flex flex-row items-center justify-between">
                  <CardTitle>Job Postings Management</CardTitle>
                </CardHeader>
                <CardContent>
                  {jobPostings.length > 0 ? (
                    <div className="space-y-4">
                      {jobPostings.map((job) => (
                        <div 
                          key={job.id} 
                          className="flex flex-col md:flex-row md:items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition-colors"
                        >
                          <div className="mb-4 md:mb-0">
                            <div className="flex items-center">
                              <p className="font-medium">{job.title}</p>
                              <Badge 
                                variant={job.status === 'Active' ? 'default' : 'secondary'}
                                className="ml-2"
                              >
                                {job.status}
                              </Badge>
                            </div>
                            <p className="text-sm">{job.company}</p>
                            <div className="flex flex-col sm:flex-row gap-1 sm:gap-4 mt-1">
                              <p className="text-sm text-muted-foreground">
                                {job.location} • {job.type}
                              </p>
                              <p className="text-sm text-muted-foreground">
                                Posted: {job.postedDate}
                              </p>
                            </div>
                          </div>
                          
                          <div className="flex gap-2">
                            {job.status === 'Pending Review' && (
                              <>
                                <Button variant="outline" size="sm" className="text-green-600">
                                  <CheckCircle className="h-4 w-4 mr-1" />
                                  Approve
                                </Button>
                                <Button variant="outline" size="sm" className="text-red-600">
                                  <XCircle className="h-4 w-4 mr-1" />
                                  Reject
                                </Button>
                              </>
                            )}
                            <Button variant="outline" size="sm" asChild>
                              <Link to={`/jobs/${job.id}`}>
                                View Details
                              </Link>
                            </Button>
                          </div>
                        </div>
                      ))}
                    </div>
                  ) : (
                    <div className="text-center py-8">
                      <Briefcase className="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                      <h3 className="text-lg font-medium mb-2">No job postings available</h3>
                      <p className="text-muted-foreground">
                        There are no job postings to manage at this time.
                      </p>
                    </div>
                  )}
                </CardContent>
              </Card>
            </TabsContent>
            
            <TabsContent value="placements">
              <Card>
                <CardHeader>
                  <CardTitle>Placement Statistics</CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="space-y-4">
                      <h3 className="text-lg font-medium">Placements by Department</h3>
                      <div className="space-y-3">
                        <div>
                          <div className="flex justify-between mb-1">
                            <span className="text-sm">Computer Science</span>
                            <span className="text-sm font-medium">42 students</span>
                          </div>
                          <div className="w-full bg-gray-200 rounded-full h-2">
                            <div className="bg-recruit-600 h-2 rounded-full" style={{ width: '85%' }}></div>
                          </div>
                        </div>
                        <div>
                          <div className="flex justify-between mb-1">
                            <span className="text-sm">Electrical Engineering</span>
                            <span className="text-sm font-medium">16 students</span>
                          </div>
                          <div className="w-full bg-gray-200 rounded-full h-2">
                            <div className="bg-recruit-600 h-2 rounded-full" style={{ width: '65%' }}></div>
                          </div>
                        </div>
                        <div>
                          <div className="flex justify-between mb-1">
                            <span className="text-sm">Mechanical Engineering</span>
                            <span className="text-sm font-medium">10 students</span>
                          </div>
                          <div className="w-full bg-gray-200 rounded-full h-2">
                            <div className="bg-recruit-600 h-2 rounded-full" style={{ width: '40%' }}></div>
                          </div>
                        </div>
                        <div>
                          <div className="flex justify-between mb-1">
                            <span className="text-sm">Business Administration</span>
                            <span className="text-sm font-medium">8 students</span>
                          </div>
                          <div className="w-full bg-gray-200 rounded-full h-2">
                            <div className="bg-recruit-600 h-2 rounded-full" style={{ width: '30%' }}></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div className="space-y-4">
                      <h3 className="text-lg font-medium">Top Recruiting Companies</h3>
                      <div className="space-y-3">
                        <div>
                          <div className="flex justify-between mb-1">
                            <span className="text-sm">TechSolutions Inc.</span>
                            <span className="text-sm font-medium">12 offers</span>
                          </div>
                          <div className="w-full bg-gray-200 rounded-full h-2">
                            <div className="bg-recruit-600 h-2 rounded-full" style={{ width: '80%' }}></div>
                          </div>
                        </div>
                        <div>
                          <div className="flex justify-between mb-1">
                            <span className="text-sm">Analytics Pro</span>
                            <span className="text-sm font-medium">8 offers</span>
                          </div>
                          <div className="w-full bg-gray-200 rounded-full h-2">
                            <div className="bg-recruit-600 h-2 rounded-full" style={{ width: '60%' }}></div>
                          </div>
                        </div>
                        <div>
                          <div className="flex justify-between mb-1">
                            <span className="text-sm">Global Finance</span>
                            <span className="text-sm font-medium">6 offers</span>
                          </div>
                          <div className="w-full bg-gray-200 rounded-full h-2">
                            <div className="bg-recruit-600 h-2 rounded-full" style={{ width: '45%' }}></div>
                          </div>
                        </div>
                        <div>
                          <div className="flex justify-between mb-1">
                            <span className="text-sm">InnovationTech</span>
                            <span className="text-sm font-medium">5 offers</span>
                          </div>
                          <div className="w-full bg-gray-200 rounded-full h-2">
                            <div className="bg-recruit-600 h-2 rounded-full" style={{ width: '35%' }}></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div className="flex justify-center mt-8">
                    <Button variant="outline" asChild>
                      <Link to="/admin/reports">
                        <FileText className="h-4 w-4 mr-2" />
                        View Detailed Reports
                      </Link>
                    </Button>
                  </div>
                </CardContent>
              </Card>
            </TabsContent>
          </Tabs>
        </div>
      </main>
      
      <Footer />
    </div>
  );
};

export default AdminDashboard;
