
import React from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import { useAuth } from '@/hooks/useAuth';
import { useToast } from '@/components/ui/use-toast';
import { Briefcase, MapPin, Calendar, Building, Clock, ArrowLeft } from 'lucide-react';

// Mock job details data
const jobsData = [
  {
    id: '1',
    title: 'Software Engineering Intern',
    company: 'TechSolutions Inc.',
    location: 'Remote',
    type: 'Internship',
    deadline: 'April 20, 2025',
    description: `
      <p>TechSolutions Inc. is seeking a motivated Software Engineering Intern to join our dynamic team for the summer of 2025. This is an excellent opportunity for students passionate about software development to gain real-world experience working on cutting-edge technology.</p>
      
      <h3>Responsibilities:</h3>
      <ul>
        <li>Collaborate with engineering teams to develop and test new features</li>
        <li>Write clean, efficient, and maintainable code</li>
        <li>Participate in code reviews and team meetings</li>
        <li>Debug and fix software issues</li>
        <li>Contribute to technical documentation</li>
      </ul>
      
      <h3>Requirements:</h3>
      <ul>
        <li>Currently pursuing a degree in Computer Science or related field</li>
        <li>Strong understanding of programming fundamentals</li>
        <li>Experience with one or more programming languages (Java, Python, JavaScript, etc.)</li>
        <li>Basic knowledge of data structures and algorithms</li>
        <li>Excellent problem-solving skills</li>
        <li>Ability to work in a fast-paced environment</li>
      </ul>
      
      <h3>Perks:</h3>
      <ul>
        <li>Competitive stipend</li>
        <li>Flexible remote work schedule</li>
        <li>Mentor support throughout the internship</li>
        <li>Networking opportunities with industry professionals</li>
        <li>Possibility of full-time employment after graduation</li>
      </ul>
    `,
    qualifications: 'Bachelor's degree in Computer Science or related field (in progress)',
    skills: ['JavaScript', 'React', 'Node.js', 'Git', 'Problem-solving'],
    salary: '$20-25/hour',
    duration: '12 weeks (Summer 2025)',
    posted: 'March 15, 2025',
  },
  {
    id: '2',
    title: 'Data Analyst',
    company: 'Analytics Pro',
    location: 'New York, NY',
    type: 'Full-time',
    deadline: 'April 25, 2025',
    description: `
      <p>Analytics Pro is looking for a detail-oriented Data Analyst to join our growing team. The ideal candidate will have experience transforming raw data into actionable insights and will help drive business decisions through data analysis.</p>
      
      <h3>Responsibilities:</h3>
      <ul>
        <li>Collect, process, and analyze large datasets</li>
        <li>Build and maintain data visualization dashboards</li>
        <li>Collaborate with cross-functional teams to understand data needs</li>
        <li>Identify trends and patterns in complex datasets</li>
        <li>Present findings to stakeholders in a clear, concise manner</li>
      </ul>
      
      <h3>Requirements:</h3>
      <ul>
        <li>Bachelor's degree in Statistics, Mathematics, Computer Science, or related field</li>
        <li>2+ years of experience in data analysis or similar role</li>
        <li>Proficiency in SQL and data manipulation tools</li>
        <li>Experience with data visualization tools like Tableau or Power BI</li>
        <li>Strong analytical and problem-solving skills</li>
        <li>Excellent communication and presentation abilities</li>
      </ul>
      
      <h3>Benefits:</h3>
      <ul>
        <li>Competitive salary and benefits package</li>
        <li>Health, dental, and vision insurance</li>
        <li>401(k) with company match</li>
        <li>Professional development opportunities</li>
        <li>Flexible work schedule</li>
      </ul>
    `,
    qualifications: 'Bachelor's degree in Statistics, Mathematics, Computer Science, or related field',
    skills: ['SQL', 'Python', 'Tableau', 'Excel', 'Statistical Analysis'],
    salary: '$70,000 - $90,000/year',
    duration: 'Permanent',
    posted: 'March 10, 2025',
  },
];

const JobDetail = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const { isAuthenticated, userRole } = useAuth();
  const { toast } = useToast();
  
  // Find the job with the matching ID
  const job = jobsData.find((job) => job.id === id);
  
  if (!job) {
    return (
      <div className="flex flex-col min-h-screen">
        <Header />
        <main className="flex-grow py-12 bg-gray-50">
          <div className="container mx-auto px-4 text-center">
            <h1 className="text-3xl font-bold mb-4">Job Not Found</h1>
            <p className="mb-6">The job listing you're looking for doesn't exist or has been removed.</p>
            <Button asChild>
              <Link to="/jobs">Browse All Jobs</Link>
            </Button>
          </div>
        </main>
        <Footer />
      </div>
    );
  }

  const handleApply = () => {
    if (!isAuthenticated) {
      toast({
        title: "Authentication Required",
        description: "Please login or register to apply for this job.",
        variant: "destructive",
      });
      return;
    }

    if (userRole === 'company' || userRole === 'admin') {
      toast({
        title: "Student Account Required",
        description: "Only students can apply for jobs.",
        variant: "destructive",
      });
      return;
    }

    // In a real app, this would submit an application
    toast({
      title: "Application Submitted!",
      description: `You've successfully applied for ${job.title} at ${job.company}.`,
    });
  };

  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow py-12 bg-gray-50">
        <div className="container mx-auto px-4">
          <Button 
            variant="ghost" 
            className="mb-6 flex items-center gap-2"
            onClick={() => navigate('/jobs')}
          >
            <ArrowLeft className="h-4 w-4" />
            Back to Jobs
          </Button>
          
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* Main Content */}
            <div className="lg:col-span-2">
              <Card>
                <CardContent className="p-8">
                  <div className="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-6">
                    <div>
                      <h1 className="text-2xl md:text-3xl font-bold mb-2">{job.title}</h1>
                      <div className="flex items-center text-muted-foreground mb-4">
                        <Building className="h-4 w-4 mr-1" />
                        <span>{job.company}</span>
                      </div>
                      <div className="flex flex-wrap gap-2">
                        <Badge variant="outline" className="flex items-center gap-1">
                          <MapPin className="h-3 w-3" />
                          {job.location}
                        </Badge>
                        <Badge 
                          variant={job.type === 'Internship' ? 'secondary' : 'default'}
                          className="flex items-center gap-1"
                        >
                          <Briefcase className="h-3 w-3" />
                          {job.type}
                        </Badge>
                        <Badge variant="outline" className="flex items-center gap-1">
                          <Clock className="h-3 w-3" />
                          {job.duration}
                        </Badge>
                      </div>
                    </div>
                    
                    <div className="mt-4 md:mt-0">
                      <Button className="w-full" onClick={handleApply}>
                        Apply Now
                      </Button>
                    </div>
                  </div>
                  
                  <div className="border-t pt-6">
                    <h2 className="text-xl font-semibold mb-4">Job Description</h2>
                    <div 
                      className="prose max-w-none prose-headings:text-lg prose-headings:font-semibold prose-headings:mt-6 prose-headings:mb-2"
                      dangerouslySetInnerHTML={{ __html: job.description }}
                    />
                  </div>
                </CardContent>
              </Card>
            </div>
            
            {/* Sidebar */}
            <div>
              <Card className="mb-6">
                <CardContent className="p-6">
                  <h3 className="font-semibold text-lg mb-4">Job Overview</h3>
                  
                  <div className="space-y-4">
                    <div>
                      <h4 className="text-sm text-muted-foreground mb-1">Salary</h4>
                      <p>{job.salary}</p>
                    </div>
                    
                    <div>
                      <h4 className="text-sm text-muted-foreground mb-1">Job Type</h4>
                      <p>{job.type}</p>
                    </div>
                    
                    <div>
                      <h4 className="text-sm text-muted-foreground mb-1">Location</h4>
                      <p>{job.location}</p>
                    </div>
                    
                    <div>
                      <h4 className="text-sm text-muted-foreground mb-1">Date Posted</h4>
                      <p>{job.posted}</p>
                    </div>
                    
                    <div>
                      <h4 className="text-sm text-muted-foreground mb-1">Application Deadline</h4>
                      <p className="flex items-center">
                        <Calendar className="h-4 w-4 mr-1 text-recruit-500" />
                        {job.deadline}
                      </p>
                    </div>
                  </div>
                </CardContent>
              </Card>
              
              <Card>
                <CardContent className="p-6">
                  <h3 className="font-semibold text-lg mb-4">Required Skills</h3>
                  
                  <div className="flex flex-wrap gap-2">
                    {job.skills.map((skill, index) => (
                      <Badge key={index} variant="secondary">
                        {skill}
                      </Badge>
                    ))}
                  </div>
                  
                  <div className="mt-6">
                    <h4 className="text-sm text-muted-foreground mb-2">Qualifications</h4>
                    <p>{job.qualifications}</p>
                  </div>
                </CardContent>
              </Card>
            </div>
          </div>
        </div>
      </main>
      
      <Footer />
    </div>
  );
};

export default JobDetail;
