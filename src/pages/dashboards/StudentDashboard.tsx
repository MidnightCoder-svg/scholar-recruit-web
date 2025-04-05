
import React from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import { Link } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { Briefcase, FileText, Bell, BookOpen, User, Clock } from 'lucide-react';

// Mock data for applications
const applications = [
  {
    id: '1',
    jobTitle: 'Software Engineering Intern',
    company: 'TechSolutions Inc.',
    appliedDate: 'March 20, 2025',
    status: 'Under Review',
  },
  {
    id: '2',
    jobTitle: 'Front-end Developer',
    company: 'WebTech',
    appliedDate: 'March 15, 2025',
    status: 'Interview Scheduled',
  },
  {
    id: '3',
    jobTitle: 'Data Analyst Intern',
    company: 'Analytics Pro',
    appliedDate: 'March 10, 2025',
    status: 'Rejected',
  },
];

// Mock data for notifications
const notifications = [
  {
    id: '1',
    message: 'Interview scheduled for Software Engineering Intern at TechSolutions Inc.',
    date: 'March 25, 2025',
    isRead: false,
  },
  {
    id: '2',
    message: 'Your application for Data Analyst Intern has been reviewed.',
    date: 'March 20, 2025',
    isRead: true,
  },
  {
    id: '3',
    message: 'New internship opportunities matching your profile are available!',
    date: 'March 18, 2025',
    isRead: true,
  },
];

const StudentDashboard = () => {
  const { user } = useAuth();
  
  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow py-12 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="flex justify-between items-center mb-8">
            <h1 className="text-2xl font-bold">Student Dashboard</h1>
            <Button asChild>
              <Link to="/profile">Edit Profile</Link>
            </Button>
          </div>
          
          {/* Stats Overview */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <Card>
              <CardContent className="flex items-center justify-between pt-6">
                <div>
                  <p className="text-sm text-muted-foreground">Applications</p>
                  <p className="text-2xl font-bold">{applications.length}</p>
                </div>
                <div className="p-2 bg-recruit-100 rounded-full">
                  <Briefcase className="h-6 w-6 text-recruit-600" />
                </div>
              </CardContent>
            </Card>
            
            <Card>
              <CardContent className="flex items-center justify-between pt-6">
                <div>
                  <p className="text-sm text-muted-foreground">Interviews</p>
                  <p className="text-2xl font-bold">1</p>
                </div>
                <div className="p-2 bg-recruit-100 rounded-full">
                  <User className="h-6 w-6 text-recruit-600" />
                </div>
              </CardContent>
            </Card>
            
            <Card>
              <CardContent className="flex items-center justify-between pt-6">
                <div>
                  <p className="text-sm text-muted-foreground">Active Notifications</p>
                  <p className="text-2xl font-bold">{notifications.filter(n => !n.isRead).length}</p>
                </div>
                <div className="p-2 bg-recruit-100 rounded-full">
                  <Bell className="h-6 w-6 text-recruit-600" />
                </div>
              </CardContent>
            </Card>
          </div>
          
          <Tabs defaultValue="applications">
            <TabsList className="mb-6">
              <TabsTrigger value="applications">Applications</TabsTrigger>
              <TabsTrigger value="notifications">Notifications</TabsTrigger>
              <TabsTrigger value="resources">Resources</TabsTrigger>
            </TabsList>
            
            <TabsContent value="applications">
              <Card>
                <CardHeader>
                  <CardTitle>Your Applications</CardTitle>
                </CardHeader>
                <CardContent>
                  {applications.length > 0 ? (
                    <div className="space-y-4">
                      {applications.map((application) => (
                        <div 
                          key={application.id} 
                          className="flex flex-col md:flex-row md:items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition-colors"
                        >
                          <div className="mb-2 md:mb-0">
                            <p className="font-medium">{application.jobTitle}</p>
                            <p className="text-sm text-muted-foreground">{application.company}</p>
                          </div>
                          
                          <div className="flex flex-col md:flex-row items-start md:items-center gap-2 md:gap-4">
                            <div className="flex items-center text-sm text-muted-foreground">
                              <Clock className="mr-1 h-3 w-3" />
                              Applied: {application.appliedDate}
                            </div>
                            
                            <Badge 
                              variant={
                                application.status === 'Rejected' ? 'destructive' : 
                                application.status === 'Interview Scheduled' ? 'default' : 'secondary'
                              }
                            >
                              {application.status}
                            </Badge>
                          </div>
                        </div>
                      ))}
                    </div>
                  ) : (
                    <div className="text-center py-8">
                      <Briefcase className="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                      <h3 className="text-lg font-medium mb-2">No applications yet</h3>
                      <p className="text-muted-foreground mb-4">
                        You haven't applied to any jobs or internships yet.
                      </p>
                      <Button asChild>
                        <Link to="/jobs">Browse Jobs</Link>
                      </Button>
                    </div>
                  )}
                </CardContent>
              </Card>
            </TabsContent>
            
            <TabsContent value="notifications">
              <Card>
                <CardHeader>
                  <CardTitle>Notifications</CardTitle>
                </CardHeader>
                <CardContent>
                  {notifications.length > 0 ? (
                    <div className="space-y-4">
                      {notifications.map((notification) => (
                        <div 
                          key={notification.id} 
                          className={`p-4 border rounded-lg ${!notification.isRead ? 'bg-recruit-50 border-recruit-200' : 'hover:bg-gray-50'} transition-colors`}
                        >
                          <div className="flex gap-4 items-start">
                            <div className={`p-2 rounded-full ${!notification.isRead ? 'bg-recruit-100' : 'bg-gray-100'}`}>
                              <Bell className={`h-4 w-4 ${!notification.isRead ? 'text-recruit-600' : 'text-gray-500'}`} />
                            </div>
                            <div>
                              <p className={`${!notification.isRead ? 'font-medium' : ''}`}>
                                {notification.message}
                              </p>
                              <p className="text-sm text-muted-foreground mt-1">
                                {notification.date}
                              </p>
                            </div>
                          </div>
                        </div>
                      ))}
                    </div>
                  ) : (
                    <div className="text-center py-8">
                      <Bell className="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                      <h3 className="text-lg font-medium mb-2">No notifications</h3>
                      <p className="text-muted-foreground">
                        You don't have any notifications at the moment.
                      </p>
                    </div>
                  )}
                </CardContent>
              </Card>
            </TabsContent>
            
            <TabsContent value="resources">
              <Card>
                <CardHeader>
                  <CardTitle>Career Resources</CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                      <div className="flex gap-4 items-start">
                        <div className="p-2 bg-recruit-100 rounded-full">
                          <FileText className="h-4 w-4 text-recruit-600" />
                        </div>
                        <div>
                          <h3 className="font-medium">Resume Building Guide</h3>
                          <p className="text-sm text-muted-foreground mt-1">
                            Learn how to create an effective resume that stands out to recruiters.
                          </p>
                          <Button variant="link" className="p-0 h-auto mt-2" asChild>
                            <a href="#" target="_blank">View Resource</a>
                          </Button>
                        </div>
                      </div>
                    </div>
                    
                    <div className="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                      <div className="flex gap-4 items-start">
                        <div className="p-2 bg-recruit-100 rounded-full">
                          <User className="h-4 w-4 text-recruit-600" />
                        </div>
                        <div>
                          <h3 className="font-medium">Interview Preparation</h3>
                          <p className="text-sm text-muted-foreground mt-1">
                            Tips and techniques to help you excel in your interviews.
                          </p>
                          <Button variant="link" className="p-0 h-auto mt-2" asChild>
                            <a href="#" target="_blank">View Resource</a>
                          </Button>
                        </div>
                      </div>
                    </div>
                    
                    <div className="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                      <div className="flex gap-4 items-start">
                        <div className="p-2 bg-recruit-100 rounded-full">
                          <BookOpen className="h-4 w-4 text-recruit-600" />
                        </div>
                        <div>
                          <h3 className="font-medium">Skill Development</h3>
                          <p className="text-sm text-muted-foreground mt-1">
                            Resources to help you develop in-demand skills for your field.
                          </p>
                          <Button variant="link" className="p-0 h-auto mt-2" asChild>
                            <a href="#" target="_blank">View Resource</a>
                          </Button>
                        </div>
                      </div>
                    </div>
                    
                    <div className="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                      <div className="flex gap-4 items-start">
                        <div className="p-2 bg-recruit-100 rounded-full">
                          <Briefcase className="h-4 w-4 text-recruit-600" />
                        </div>
                        <div>
                          <h3 className="font-medium">Industry Insights</h3>
                          <p className="text-sm text-muted-foreground mt-1">
                            Stay up-to-date with trends and insights in your industry.
                          </p>
                          <Button variant="link" className="p-0 h-auto mt-2" asChild>
                            <a href="#" target="_blank">View Resource</a>
                          </Button>
                        </div>
                      </div>
                    </div>
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

export default StudentDashboard;
