
import React from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import { Link } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { Briefcase, Users, Bell, Plus, Download, Eye, CheckCircle, XCircle } from 'lucide-react';

// Mock data for job postings
const jobPostings = [
  {
    id: '1',
    title: 'Software Engineering Intern',
    location: 'Remote',
    type: 'Internship',
    postedDate: 'March 15, 2025',
    applicants: 12,
    status: 'Active',
  },
  {
    id: '2',
    title: 'Frontend Developer',
    location: 'New York, NY',
    type: 'Full-time',
    postedDate: 'March 10, 2025',
    applicants: 8,
    status: 'Active',
  },
];

// Mock data for applications
const applications = [
  {
    id: '1',
    jobId: '1',
    student: 'John Doe',
    college: 'MIT',
    appliedDate: 'March 20, 2025',
    status: 'Under Review',
  },
  {
    id: '2',
    jobId: '1',
    student: 'Jane Smith',
    college: 'Stanford University',
    appliedDate: 'March 18, 2025',
    status: 'Interview Scheduled',
  },
  {
    id: '3',
    jobId: '2',
    student: 'Michael Johnson',
    college: 'UC Berkeley',
    appliedDate: 'March 15, 2025',
    status: 'Rejected',
  },
];

const CompanyDashboard = () => {
  const { user } = useAuth();
  
  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow py-12 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="flex justify-between items-center mb-8">
            <h1 className="text-2xl font-bold">Company Dashboard</h1>
            <div className="flex gap-3">
              <Button variant="outline" asChild>
                <Link to="/company/profile">Edit Profile</Link>
              </Button>
              <Button asChild>
                <Link to="/company/post-job">
                  <Plus className="h-4 w-4 mr-2" />
                  Post New Job
                </Link>
              </Button>
            </div>
          </div>
          
          {/* Stats Overview */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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
                  <p className="text-sm text-muted-foreground">Total Applicants</p>
                  <p className="text-2xl font-bold">{applications.length}</p>
                </div>
                <div className="p-2 bg-recruit-100 rounded-full">
                  <Users className="h-6 w-6 text-recruit-600" />
                </div>
              </CardContent>
            </Card>
            
            <Card>
              <CardContent className="flex items-center justify-between pt-6">
                <div>
                  <p className="text-sm text-muted-foreground">Scheduled Interviews</p>
                  <p className="text-2xl font-bold">{applications.filter(app => app.status === 'Interview Scheduled').length}</p>
                </div>
                <div className="p-2 bg-recruit-100 rounded-full">
                  <Bell className="h-6 w-6 text-recruit-600" />
                </div>
              </CardContent>
            </Card>
          </div>
          
          <Tabs defaultValue="postings">
            <TabsList className="mb-6">
              <TabsTrigger value="postings">Job Postings</TabsTrigger>
              <TabsTrigger value="applications">Applications</TabsTrigger>
            </TabsList>
            
            <TabsContent value="postings">
              <Card>
                <CardHeader className="flex flex-row items-center justify-between">
                  <CardTitle>Your Job Postings</CardTitle>
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
                            <div className="flex items-center gap-4 mt-1">
                              <p className="text-sm text-muted-foreground">{job.location}</p>
                              <Badge variant="outline">{job.type}</Badge>
                            </div>
                            <p className="text-sm text-muted-foreground mt-1">
                              Posted: {job.postedDate} • {job.applicants} applicants
                            </p>
                          </div>
                          
                          <div className="flex gap-2">
                            <Button variant="outline" size="sm" asChild>
                              <Link to={`/jobs/${job.id}`}>
                                <Eye className="h-4 w-4 mr-1" />
                                View
                              </Link>
                            </Button>
                            <Button variant="outline" size="sm" asChild>
                              <Link to={`/company/edit-job/${job.id}`}>
                                Edit
                              </Link>
                            </Button>
                          </div>
                        </div>
                      ))}
                    </div>
                  ) : (
                    <div className="text-center py-8">
                      <Briefcase className="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                      <h3 className="text-lg font-medium mb-2">No job postings yet</h3>
                      <p className="text-muted-foreground mb-4">
                        You haven't posted any jobs or internships yet.
                      </p>
                      <Button asChild>
                        <Link to="/company/post-job">Post a Job</Link>
                      </Button>
                    </div>
                  )}
                </CardContent>
              </Card>
            </TabsContent>
            
            <TabsContent value="applications">
              <Card>
                <CardHeader>
                  <CardTitle>Applications Received</CardTitle>
                </CardHeader>
                <CardContent>
                  {applications.length > 0 ? (
                    <div className="space-y-4">
                      {applications.map((application) => {
                        const job = jobPostings.find(j => j.id === application.jobId);
                        return (
                          <div 
                            key={application.id} 
                            className="flex flex-col md:flex-row md:items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition-colors"
                          >
                            <div className="mb-4 md:mb-0">
                              <p className="font-medium">{application.student}</p>
                              <p className="text-sm">{application.college}</p>
                              <div className="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 mt-1">
                                <p className="text-sm text-muted-foreground">
                                  Applied for: {job?.title || 'Unknown Position'}
                                </p>
                                <p className="text-sm text-muted-foreground">
                                  On: {application.appliedDate}
                                </p>
                              </div>
                            </div>
                            
                            <div className="flex items-center gap-2">
                              <Badge 
                                variant={
                                  application.status === 'Rejected' ? 'destructive' : 
                                  application.status === 'Interview Scheduled' ? 'default' : 'secondary'
                                }
                              >
                                {application.status}
                              </Badge>
                              
                              <Button variant="outline" size="sm" asChild>
                                <a href="#">
                                  <Download className="h-4 w-4 mr-1" />
                                  Resume
                                </a>
                              </Button>
                              
                              {application.status === 'Under Review' && (
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
                            </div>
                          </div>
                        );
                      })}
                    </div>
                  ) : (
                    <div className="text-center py-8">
                      <Users className="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                      <h3 className="text-lg font-medium mb-2">No applications yet</h3>
                      <p className="text-muted-foreground mb-4">
                        You haven't received any applications for your job postings yet.
                      </p>
                    </div>
                  )}
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

export default CompanyDashboard;
