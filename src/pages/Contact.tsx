
import React from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import { MapPin, Mail, Phone } from 'lucide-react';
import { useToast } from '@/components/ui/use-toast';

const Contact = () => {
  const { toast } = useToast();
  
  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    // In a real app, this would submit the form data to an API
    toast({
      title: "Message sent!",
      description: "We'll get back to you as soon as possible.",
    });
    
    // Reset form
    const form = e.target as HTMLFormElement;
    form.reset();
  };
  
  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow">
        {/* Hero Section */}
        <section className="py-16 px-4 bg-gray-50">
          <div className="container mx-auto">
            <div className="max-w-3xl mx-auto text-center">
              <h1 className="text-3xl font-bold mb-6 md:text-4xl">Contact Us</h1>
              <p className="text-lg text-muted-foreground">
                Have questions or need assistance? We're here to help! Reach out to our team through any of the channels below.
              </p>
            </div>
          </div>
        </section>
        
        {/* Contact Information Section */}
        <section className="py-16 px-4">
          <div className="container mx-auto">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
              <Card>
                <CardContent className="p-6 flex flex-col items-center text-center">
                  <div className="h-12 w-12 bg-recruit-100 rounded-full flex items-center justify-center mb-4">
                    <MapPin className="h-6 w-6 text-recruit-600" />
                  </div>
                  <h3 className="text-lg font-semibold mb-2">Our Location</h3>
                  <p className="text-muted-foreground">
                    123 Tech Avenue<br />
                    San Francisco, CA 94107<br />
                    United States
                  </p>
                </CardContent>
              </Card>
              
              <Card>
                <CardContent className="p-6 flex flex-col items-center text-center">
                  <div className="h-12 w-12 bg-recruit-100 rounded-full flex items-center justify-center mb-4">
                    <Mail className="h-6 w-6 text-recruit-600" />
                  </div>
                  <h3 className="text-lg font-semibold mb-2">Email Us</h3>
                  <p className="text-muted-foreground mb-2">
                    General Inquiries:<br />
                    <a href="mailto:info@scholarrecruit.com" className="text-recruit-600 hover:underline">
                      info@scholarrecruit.com
                    </a>
                  </p>
                  <p className="text-muted-foreground">
                    Support:<br />
                    <a href="mailto:support@scholarrecruit.com" className="text-recruit-600 hover:underline">
                      support@scholarrecruit.com
                    </a>
                  </p>
                </CardContent>
              </Card>
              
              <Card>
                <CardContent className="p-6 flex flex-col items-center text-center">
                  <div className="h-12 w-12 bg-recruit-100 rounded-full flex items-center justify-center mb-4">
                    <Phone className="h-6 w-6 text-recruit-600" />
                  </div>
                  <h3 className="text-lg font-semibold mb-2">Call Us</h3>
                  <p className="text-muted-foreground mb-2">
                    Office Hours:<br />
                    Monday - Friday, 9am - 5pm PST
                  </p>
                  <p className="text-muted-foreground">
                    Phone:<br />
                    <a href="tel:+1-800-123-4567" className="text-recruit-600 hover:underline">
                      +1 (800) 123-4567
                    </a>
                  </p>
                </CardContent>
              </Card>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-12">
              <div>
                <h2 className="text-2xl font-bold mb-6">Send Us a Message</h2>
                <form onSubmit={handleSubmit} className="space-y-6">
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div className="space-y-2">
                      <label htmlFor="name" className="text-sm font-medium">
                        Your Name
                      </label>
                      <Input id="name" placeholder="John Doe" required />
                    </div>
                    <div className="space-y-2">
                      <label htmlFor="email" className="text-sm font-medium">
                        Email Address
                      </label>
                      <Input id="email" type="email" placeholder="john@example.com" required />
                    </div>
                  </div>
                  
                  <div className="space-y-2">
                    <label htmlFor="subject" className="text-sm font-medium">
                      Subject
                    </label>
                    <Input id="subject" placeholder="How can we help you?" required />
                  </div>
                  
                  <div className="space-y-2">
                    <label htmlFor="message" className="text-sm font-medium">
                      Message
                    </label>
                    <Textarea 
                      id="message" 
                      placeholder="Enter your message here..." 
                      rows={6} 
                      required 
                    />
                  </div>
                  
                  <Button type="submit">Send Message</Button>
                </form>
              </div>
              
              <div>
                <h2 className="text-2xl font-bold mb-6">FAQs</h2>
                <div className="space-y-6">
                  <div>
                    <h3 className="text-lg font-semibold mb-2">How do I create an account?</h3>
                    <p className="text-muted-foreground">
                      To create an account, click on the "Register" button in the top right corner of the homepage. Choose the appropriate account type (Student, Company, or Admin) and follow the registration process.
                    </p>
                  </div>
                  
                  <div>
                    <h3 className="text-lg font-semibold mb-2">Is the platform free to use?</h3>
                    <p className="text-muted-foreground">
                      ScholarRecruit is free for students. Companies may have premium features available for a subscription fee, depending on their recruitment needs.
                    </p>
                  </div>
                  
                  <div>
                    <h3 className="text-lg font-semibold mb-2">How long does account approval take?</h3>
                    <p className="text-muted-foreground">
                      Student accounts are typically approved within 24 hours. Company accounts may take 1-2 business days for verification purposes.
                    </p>
                  </div>
                  
                  <div>
                    <h3 className="text-lg font-semibold mb-2">Can I update my profile after registration?</h3>
                    <p className="text-muted-foreground">
                      Yes, you can update your profile information at any time by accessing your dashboard and clicking on the "Edit Profile" button.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        
        {/* Map Section */}
        <section className="bg-gray-50 py-8 px-4">
          <div className="container mx-auto">
            <div className="bg-gray-300 h-96 rounded-lg">
              {/* Map would be integrated here in a real application */}
              <div className="flex items-center justify-center h-full">
                <p className="text-muted-foreground">Interactive Map Coming Soon</p>
              </div>
            </div>
          </div>
        </section>
      </main>
      
      <Footer />
    </div>
  );
};

export default Contact;
