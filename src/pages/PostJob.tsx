
import React from 'react';
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { useNavigate } from 'react-router-dom';
import { 
  Briefcase, MapPin, Clock, Calendar, Award, DollarSign, FileText 
} from "lucide-react";

import Header from '@/components/Header';
import Footer from '@/components/Footer';
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { useToast } from "@/hooks/use-toast";

// Define form schema
const formSchema = z.object({
  title: z.string().min(5, { message: "Job title must be at least 5 characters." }),
  location: z.string().min(3, { message: "Location is required." }),
  type: z.enum(["Full-time", "Part-time", "Contract", "Internship", "Remote"]),
  duration: z.string().optional(),
  salary: z.string().optional(),
  requirements: z.string().min(10, { message: "Requirements must be at least 10 characters." }),
  description: z.string().min(20, { message: "Description must be at least 20 characters." }),
  deadline: z.string().optional(),
});

type FormValues = z.infer<typeof formSchema>;

const PostJob = () => {
  const { toast } = useToast();
  const navigate = useNavigate();
  
  // Initialize form with default values
  const form = useForm<FormValues>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      title: "",
      location: "",
      type: "Full-time",
      duration: "",
      salary: "",
      requirements: "",
      description: "",
      deadline: "",
    },
  });

  // Handle form submission
  const onSubmit = async (data: FormValues) => {
    // In a real application, this would send the data to a backend API
    console.log("Job posting data:", data);
    
    // Show success toast
    toast({
      title: "Job Posted",
      description: "Your job has been successfully posted.",
    });
    
    // Navigate back to the dashboard
    navigate("/company/dashboard");
  };

  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow py-12 bg-gray-50">
        <div className="container max-w-3xl mx-auto px-4">
          <h1 className="text-2xl font-bold mb-8">Post a New Job</h1>
          
          <Card>
            <CardHeader>
              <CardTitle>Job Details</CardTitle>
              <CardDescription>
                Complete the form below to create a new job posting.
              </CardDescription>
            </CardHeader>
            
            <CardContent>
              <Form {...form}>
                <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
                  <FormField
                    control={form.control}
                    name="title"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Job Title</FormLabel>
                        <FormControl>
                          <div className="flex">
                            <div className="bg-muted flex items-center px-3 rounded-l-md border border-r-0 border-input">
                              <Briefcase className="h-4 w-4 text-muted-foreground" />
                            </div>
                            <Input 
                              {...field} 
                              className="rounded-l-none"
                              placeholder="e.g. Software Engineering Intern" 
                            />
                          </div>
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <FormField
                      control={form.control}
                      name="location"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Location</FormLabel>
                          <FormControl>
                            <div className="flex">
                              <div className="bg-muted flex items-center px-3 rounded-l-md border border-r-0 border-input">
                                <MapPin className="h-4 w-4 text-muted-foreground" />
                              </div>
                              <Input 
                                {...field} 
                                className="rounded-l-none"
                                placeholder="e.g. New York, NY or Remote" 
                              />
                            </div>
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    
                    <FormField
                      control={form.control}
                      name="type"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Job Type</FormLabel>
                          <Select 
                            onValueChange={field.onChange} 
                            defaultValue={field.value}
                          >
                            <FormControl>
                              <SelectTrigger>
                                <SelectValue placeholder="Select job type" />
                              </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                              <SelectItem value="Full-time">Full-time</SelectItem>
                              <SelectItem value="Part-time">Part-time</SelectItem>
                              <SelectItem value="Contract">Contract</SelectItem>
                              <SelectItem value="Internship">Internship</SelectItem>
                              <SelectItem value="Remote">Remote</SelectItem>
                            </SelectContent>
                          </Select>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </div>
                  
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <FormField
                      control={form.control}
                      name="duration"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Duration (if applicable)</FormLabel>
                          <FormControl>
                            <div className="flex">
                              <div className="bg-muted flex items-center px-3 rounded-l-md border border-r-0 border-input">
                                <Clock className="h-4 w-4 text-muted-foreground" />
                              </div>
                              <Input 
                                {...field} 
                                className="rounded-l-none"
                                placeholder="e.g. 3 months, 6 months, etc." 
                              />
                            </div>
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    
                    <FormField
                      control={form.control}
                      name="salary"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Salary/Stipend</FormLabel>
                          <FormControl>
                            <div className="flex">
                              <div className="bg-muted flex items-center px-3 rounded-l-md border border-r-0 border-input">
                                <DollarSign className="h-4 w-4 text-muted-foreground" />
                              </div>
                              <Input 
                                {...field} 
                                className="rounded-l-none"
                                placeholder="e.g. $20/hr or $60,000/year" 
                              />
                            </div>
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </div>
                  
                  <FormField
                    control={form.control}
                    name="deadline"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Application Deadline</FormLabel>
                        <FormControl>
                          <div className="flex">
                            <div className="bg-muted flex items-center px-3 rounded-l-md border border-r-0 border-input">
                              <Calendar className="h-4 w-4 text-muted-foreground" />
                            </div>
                            <Input 
                              {...field} 
                              type="date"
                              className="rounded-l-none"
                            />
                          </div>
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  
                  <FormField
                    control={form.control}
                    name="requirements"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Requirements</FormLabel>
                        <FormControl>
                          <div className="flex">
                            <div className="bg-muted flex items-center h-24 px-3 rounded-l-md border border-r-0 border-input">
                              <Award className="h-4 w-4 text-muted-foreground" />
                            </div>
                            <Textarea 
                              {...field} 
                              className="rounded-l-none min-h-24"
                              placeholder="List the skills, qualifications, and experience required" 
                            />
                          </div>
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  
                  <FormField
                    control={form.control}
                    name="description"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Job Description</FormLabel>
                        <FormControl>
                          <div className="flex">
                            <div className="bg-muted flex items-center h-24 px-3 rounded-l-md border border-r-0 border-input">
                              <FileText className="h-4 w-4 text-muted-foreground" />
                            </div>
                            <Textarea 
                              {...field} 
                              className="rounded-l-none min-h-24"
                              placeholder="Provide a detailed description of the job, responsibilities, and benefits" 
                            />
                          </div>
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  
                  <div className="flex justify-end space-x-4">
                    <Button 
                      type="button" 
                      variant="outline" 
                      onClick={() => navigate("/company/dashboard")}
                    >
                      Cancel
                    </Button>
                    <Button type="submit">Post Job</Button>
                  </div>
                </form>
              </Form>
            </CardContent>
          </Card>
        </div>
      </main>
      
      <Footer />
    </div>
  );
};

export default PostJob;
