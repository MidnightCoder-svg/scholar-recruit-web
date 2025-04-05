
import React from 'react';
import { Card, CardContent } from '@/components/ui/card';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import { ArrowRight, Users, Building, User } from 'lucide-react';

const About = () => {
  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow">
        {/* Hero Section */}
        <section className="py-16 px-4 bg-gray-50">
          <div className="container mx-auto">
            <div className="max-w-3xl mx-auto text-center">
              <h1 className="text-3xl font-bold mb-6 md:text-4xl">About ScholarRecruit</h1>
              <p className="text-lg text-muted-foreground mb-8">
                Connecting talented students with their dream employers through a streamlined and efficient recruitment platform.
              </p>
            </div>
          </div>
        </section>
        
        {/* Mission Section */}
        <section className="py-16 px-4">
          <div className="container mx-auto">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
              <div>
                <h2 className="text-2xl font-bold mb-4">Our Mission</h2>
                <p className="text-muted-foreground mb-4">
                  At ScholarRecruit, we believe in bridging the gap between academic excellence and professional opportunities. Our mission is to create a seamless connection between educational institutions, students, and employers.
                </p>
                <p className="text-muted-foreground mb-4">
                  We strive to provide a platform that democratizes access to quality job opportunities and talented graduates, making the recruitment process efficient, transparent, and effective for all parties involved.
                </p>
                <p className="text-muted-foreground">
                  By facilitating these connections, we aim to contribute to building the workforce of tomorrow and help students transition smoothly from academia to industry.
                </p>
              </div>
              <div className="flex justify-center">
                <div className="relative w-full max-w-md">
                  <div className="absolute -top-4 -right-4 w-full h-full bg-recruit-100 rounded-lg z-0"></div>
                  <div className="relative z-10 bg-white p-6 rounded-lg shadow-sm">
                    <blockquote className="italic text-muted-foreground">
                      "Our vision is to become the definitive platform that educational institutions rely on for placing their students, that students trust for launching their careers, and that employers value for finding their next generation of talent."
                    </blockquote>
                    <div className="mt-4 flex items-center">
                      <div className="h-10 w-10 rounded-full bg-recruit-600 flex items-center justify-center text-white font-bold">
                        SM
                      </div>
                      <div className="ml-4">
                        <p className="font-medium">Dr. Sarah Mitchell</p>
                        <p className="text-sm text-muted-foreground">Founder, ScholarRecruit</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        
        {/* How It Works Section */}
        <section className="py-16 px-4 bg-gray-50">
          <div className="container mx-auto">
            <div className="max-w-3xl mx-auto text-center mb-12">
              <h2 className="text-3xl font-bold mb-4">How ScholarRecruit Works</h2>
              <p className="text-muted-foreground">
                Our platform connects three key stakeholders in a seamless recruitment ecosystem.
              </p>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              <Card>
                <CardContent className="p-6 flex flex-col items-center text-center">
                  <div className="h-16 w-16 bg-recruit-100 rounded-full flex items-center justify-center mb-4">
                    <Users className="h-8 w-8 text-recruit-600" />
                  </div>
                  <h3 className="text-xl font-semibold mb-2">For Students</h3>
                  <p className="text-muted-foreground mb-4">
                    Create a compelling profile, upload your resume, and apply to opportunities that match your skills and aspirations.
                  </p>
                  <ul className="text-left space-y-2 text-sm">
                    <li className="flex items-start">
                      <ArrowRight className="h-4 w-4 mr-2 mt-1 text-recruit-600 shrink-0" />
                      <span>Access to exclusive job and internship listings</span>
                    </li>
                    <li className="flex items-start">
                      <ArrowRight className="h-4 w-4 mr-2 mt-1 text-recruit-600 shrink-0" />
                      <span>Track application status in real-time</span>
                    </li>
                    <li className="flex items-start">
                      <ArrowRight className="h-4 w-4 mr-2 mt-1 text-recruit-600 shrink-0" />
                      <span>Receive notifications for relevant opportunities</span>
                    </li>
                    <li className="flex items-start">
                      <ArrowRight className="h-4 w-4 mr-2 mt-1 text-recruit-600 shrink-0" />
                      <span>Access career resources and guidance</span>
                    </li>
                  </ul>
                </CardContent>
              </Card>
              
              <Card>
                <CardContent className="p-6 flex flex-col items-center text-center">
                  <div className="h-16 w-16 bg-recruit-100 rounded-full flex items-center justify-center mb-4">
                    <Building className="h-8 w-8 text-recruit-600" />
                  </div>
                  <h3 className="text-xl font-semibold mb-2">For Companies</h3>
                  <p className="text-muted-foreground mb-4">
                    Post job opportunities, review qualified applications, and connect with top talent from leading educational institutions.
                  </p>
                  <ul className="text-left space-y-2 text-sm">
                    <li className="flex items-start">
                      <ArrowRight className="h-4 w-4 mr-2 mt-1 text-recruit-600 shrink-0" />
                      <span>Access a curated pool of qualified candidates</span>
                    </li>
                    <li className="flex items-start">
                      <ArrowRight className="h-4 w-4 mr-2 mt-1 text-recruit-600 shrink-0" />
                      <span>Streamlined application review process</span>
                    </li>
                    <li className="flex items-start">
                      <ArrowRight className="h-4 w-4 mr-2 mt-1 text-recruit-600 shrink-0" />
                      <span>Direct communication with potential hires</span>
                    </li>
                    <li className="flex items-start">
                      <ArrowRight className="h-4 w-4 mr-2 mt-1 text-recruit-600 shrink-0" />
                      <span>Enhanced employer branding opportunities</span>
                    </li>
                  </ul>
                </CardContent>
              </Card>
              
              <Card>
                <CardContent className="p-6 flex flex-col items-center text-center">
                  <div className="h-16 w-16 bg-recruit-100 rounded-full flex items-center justify-center mb-4">
                    <User className="h-8 w-8 text-recruit-600" />
                  </div>
                  <h3 className="text-xl font-semibold mb-2">For Administrators</h3>
                  <p className="text-muted-foreground mb-4">
                    Manage the entire recruitment ecosystem, approve accounts, monitor activities, and generate insightful reports.
                  </p>
                  <ul className="text-left space-y-2 text-sm">
                    <li className="flex items-start">
                      <ArrowRight className="h-4 w-4 mr-2 mt-1 text-recruit-600 shrink-0" />
                      <span>Comprehensive dashboard with key metrics</span>
                    </li>
                    <li className="flex items-start">
                      <ArrowRight className="h-4 w-4 mr-2 mt-1 text-recruit-600 shrink-0" />
                      <span>Quality control through account verification</span>
                    </li>
                    <li className="flex items-start">
                      <ArrowRight className="h-4 w-4 mr-2 mt-1 text-recruit-600 shrink-0" />
                      <span>Communication tools for announcements</span>
                    </li>
                    <li className="flex items-start">
                      <ArrowRight className="h-4 w-4 mr-2 mt-1 text-recruit-600 shrink-0" />
                      <span>Detailed placement and analytics reports</span>
                    </li>
                  </ul>
                </CardContent>
              </Card>
            </div>
          </div>
        </section>
        
        {/* Team Section */}
        <section className="py-16 px-4">
          <div className="container mx-auto">
            <div className="max-w-3xl mx-auto text-center mb-12">
              <h2 className="text-3xl font-bold mb-4">Our Team</h2>
              <p className="text-muted-foreground">
                Meet the dedicated professionals behind ScholarRecruit who are passionate about connecting talent with opportunity.
              </p>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              <div className="text-center">
                <div className="h-32 w-32 bg-gray-200 rounded-full mx-auto mb-4"></div>
                <h3 className="text-xl font-semibold">Dr. Sarah Mitchell</h3>
                <p className="text-muted-foreground">Founder & CEO</p>
              </div>
              
              <div className="text-center">
                <div className="h-32 w-32 bg-gray-200 rounded-full mx-auto mb-4"></div>
                <h3 className="text-xl font-semibold">Michael Rodriguez</h3>
                <p className="text-muted-foreground">Chief Technology Officer</p>
              </div>
              
              <div className="text-center">
                <div className="h-32 w-32 bg-gray-200 rounded-full mx-auto mb-4"></div>
                <h3 className="text-xl font-semibold">Jennifer Lee</h3>
                <p className="text-muted-foreground">Head of University Relations</p>
              </div>
            </div>
          </div>
        </section>
      </main>
      
      <Footer />
    </div>
  );
};

export default About;
