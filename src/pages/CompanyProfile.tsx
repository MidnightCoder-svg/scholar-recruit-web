
import React, { useState } from 'react';
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { useNavigate } from 'react-router-dom';
import { Building2, Mail, Phone, MapPin, Globe, FileText } from "lucide-react";

import Header from '@/components/Header';
import Footer from '@/components/Footer';
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
import { Form, FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { useToast } from "@/hooks/use-toast";
import { useAuth } from "@/hooks/useAuth";

// Define form schema
const formSchema = z.object({
  name: z.string().min(2, { message: "Company name must be at least 2 characters." }),
  email: z.string().email({ message: "Please enter a valid email address." }),
  phone: z.string().optional(),
  website: z.string().optional(),
  location: z.string().optional(),
  description: z.string().optional(),
});

type FormValues = z.infer<typeof formSchema>;

const CompanyProfile = () => {
  const { user, updateProfile, updateProfilePhoto } = useAuth();
  const { toast } = useToast();
  const navigate = useNavigate();
  const [photoFile, setPhotoFile] = useState<File | null>(null);
  
  // Initialize form with user data
  const form = useForm<FormValues>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      name: user?.name || "",
      email: user?.email || "",
      phone: user?.phone || "",
      website: user?.website || "",
      location: user?.location || "",
      description: user?.description || "",
    },
  });

  // Handle profile photo upload
  const handlePhotoUpload = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files[0]) {
      setPhotoFile(e.target.files[0]);
    }
  };

  // Handle form submission
  const onSubmit = async (data: FormValues) => {
    try {
      // First update the profile data
      const success = await updateProfile(data);
      
      // Then update the photo if one was selected
      if (success && photoFile) {
        await updateProfilePhoto(photoFile);
      }
      
      toast({
        title: "Profile updated",
        description: "Your company profile has been successfully updated.",
      });
      
      navigate("/company/dashboard");
    } catch (error) {
      toast({
        title: "Update failed",
        description: "There was a problem updating your profile.",
        variant: "destructive",
      });
    }
  };

  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow py-12 bg-gray-50">
        <div className="container max-w-3xl mx-auto px-4">
          <h1 className="text-2xl font-bold mb-8">Company Profile</h1>
          
          <Card>
            <CardHeader>
              <CardTitle>Edit Profile</CardTitle>
              <CardDescription>
                Update your company information to help students learn more about you.
              </CardDescription>
            </CardHeader>
            
            <CardContent>
              <div className="mb-6 flex flex-col items-center">
                <div className="relative mb-4">
                  <Avatar className="w-24 h-24 border-2 border-gray-200">
                    <AvatarImage src={user?.photoUrl || ""} alt={user?.name || "Company"} />
                    <AvatarFallback className="text-lg">
                      {user?.name?.charAt(0) || "C"}
                    </AvatarFallback>
                  </Avatar>
                  
                  <div className="mt-2 flex justify-center">
                    <label htmlFor="photo-upload" className="cursor-pointer">
                      <Button variant="outline" size="sm" type="button">
                        Upload Logo
                      </Button>
                      <input
                        id="photo-upload"
                        type="file"
                        accept="image/*"
                        className="hidden"
                        onChange={handlePhotoUpload}
                      />
                    </label>
                  </div>
                </div>
                
                {photoFile && (
                  <p className="text-sm text-muted-foreground">
                    Selected: {photoFile.name}
                  </p>
                )}
              </div>
              
              <Form {...form}>
                <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
                  <FormField
                    control={form.control}
                    name="name"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Company Name</FormLabel>
                        <FormControl>
                          <div className="flex">
                            <div className="bg-muted flex items-center px-3 rounded-l-md border border-r-0 border-input">
                              <Building2 className="h-4 w-4 text-muted-foreground" />
                            </div>
                            <Input 
                              {...field} 
                              className="rounded-l-none"
                              placeholder="Enter company name" 
                            />
                          </div>
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  
                  <FormField
                    control={form.control}
                    name="email"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Email</FormLabel>
                        <FormControl>
                          <div className="flex">
                            <div className="bg-muted flex items-center px-3 rounded-l-md border border-r-0 border-input">
                              <Mail className="h-4 w-4 text-muted-foreground" />
                            </div>
                            <Input 
                              {...field} 
                              className="rounded-l-none"
                              placeholder="Enter company email" 
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
                      name="phone"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Phone</FormLabel>
                          <FormControl>
                            <div className="flex">
                              <div className="bg-muted flex items-center px-3 rounded-l-md border border-r-0 border-input">
                                <Phone className="h-4 w-4 text-muted-foreground" />
                              </div>
                              <Input 
                                {...field} 
                                className="rounded-l-none"
                                placeholder="Enter phone number" 
                              />
                            </div>
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    
                    <FormField
                      control={form.control}
                      name="website"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Website</FormLabel>
                          <FormControl>
                            <div className="flex">
                              <div className="bg-muted flex items-center px-3 rounded-l-md border border-r-0 border-input">
                                <Globe className="h-4 w-4 text-muted-foreground" />
                              </div>
                              <Input 
                                {...field} 
                                className="rounded-l-none"
                                placeholder="Enter company website" 
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
                              placeholder="Enter company location" 
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
                        <FormLabel>Company Description</FormLabel>
                        <FormControl>
                          <div className="flex">
                            <div className="bg-muted flex items-center h-24 px-3 rounded-l-md border border-r-0 border-input">
                              <FileText className="h-4 w-4 text-muted-foreground" />
                            </div>
                            <Textarea 
                              {...field} 
                              className="rounded-l-none min-h-24"
                              placeholder="Enter company description" 
                            />
                          </div>
                        </FormControl>
                        <FormDescription>
                          Describe your company, mission, values, and what you're looking for in candidates.
                        </FormDescription>
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
                    <Button type="submit">Save Changes</Button>
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

export default CompanyProfile;
