
import React, { useState, useRef } from 'react';
import { useNavigate } from 'react-router-dom';
import { z } from 'zod';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useAuth } from '@/hooks/useAuth';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import { User, Pencil, Upload, Image, Camera } from 'lucide-react';
import { useToast } from '@/hooks/use-toast';

// Form validation schema
const profileFormSchema = z.object({
  name: z.string().min(2, { message: 'Name must be at least 2 characters' }),
  email: z.string().email({ message: 'Please enter a valid email address' }).optional(),
  bio: z.string().optional(),
  education: z.string().optional(),
  experience: z.string().optional(),
  phone: z.string().optional(),
  skills: z.string().optional(),
});

type ProfileFormValues = z.infer<typeof profileFormSchema>;

const StudentProfile = () => {
  const navigate = useNavigate();
  const { user, updateProfile, updateProfilePhoto } = useAuth();
  const { toast } = useToast();
  const [isLoading, setIsLoading] = useState(false);
  const [isUploading, setIsUploading] = useState(false);
  const fileInputRef = useRef<HTMLInputElement>(null);
  
  // Initialize the form with current user data
  const form = useForm<ProfileFormValues>({
    resolver: zodResolver(profileFormSchema),
    defaultValues: {
      name: user?.name || '',
      email: user?.email || '',
      bio: user?.bio || '',
      education: user?.education || '',
      experience: user?.experience || '',
      phone: user?.phone || '',
      skills: user?.skills ? user.skills.join(', ') : '',
    },
  });

  const onSubmit = async (data: ProfileFormValues) => {
    setIsLoading(true);
    try {
      // Process skills from comma-separated string to array
      const formattedData = {
        ...data,
        skills: data.skills ? data.skills.split(',').map(skill => skill.trim()) : undefined,
      };
      
      const success = await updateProfile(formattedData);
      if (success) {
        // Navigate back to dashboard after successful update
        navigate('/student/dashboard');
      }
    } finally {
      setIsLoading(false);
    }
  };

  const handlePhotoClick = () => {
    // Trigger the hidden file input when the avatar is clicked
    if (fileInputRef.current) {
      fileInputRef.current.click();
    }
  };

  const handlePhotoChange = async (event: React.ChangeEvent<HTMLInputElement>) => {
    const files = event.target.files;
    if (!files || files.length === 0) return;

    const file = files[0];
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
      toast({
        title: 'Invalid file type',
        description: 'Please upload an image file (JPEG, PNG, etc.).',
        variant: 'destructive',
      });
      return;
    }
    
    // Validate file size (max 5MB)
    const maxSize = 5 * 1024 * 1024; // 5MB
    if (file.size > maxSize) {
      toast({
        title: 'File too large',
        description: 'Please upload an image smaller than 5MB.',
        variant: 'destructive',
      });
      return;
    }
    
    try {
      setIsUploading(true);
      const success = await updateProfilePhoto(file);
      if (success) {
        toast({
          title: 'Photo updated',
          description: 'Your profile photo has been updated successfully.',
        });
      }
    } catch (error) {
      console.error('Error uploading photo:', error);
      toast({
        title: 'Upload failed',
        description: 'There was an error uploading your photo.',
        variant: 'destructive',
      });
    } finally {
      setIsUploading(false);
      // Reset the file input
      if (fileInputRef.current) {
        fileInputRef.current.value = '';
      }
    }
  };

  // If not logged in or not a student, navigate away
  if (!user || user.role !== 'student') {
    navigate('/login');
    return null;
  }

  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow py-12 bg-gray-50">
        <div className="container mx-auto px-4 max-w-3xl">
          <div className="flex justify-between items-center mb-8">
            <h1 className="text-2xl font-bold">Edit Profile</h1>
            <Button 
              variant="outline" 
              onClick={() => navigate('/student/dashboard')}
            >
              Back to Dashboard
            </Button>
          </div>
          
          <Card>
            <CardHeader className="pb-4">
              <CardTitle className="flex items-center gap-4">
                <div className="relative group">
                  <Avatar className="h-20 w-20 cursor-pointer hover:opacity-80 transition-opacity" onClick={handlePhotoClick}>
                    <AvatarImage src={user?.photoUrl || ""} alt={user?.name} />
                    <AvatarFallback className="bg-primary text-primary-foreground text-xl">
                      {user?.name.substring(0, 2).toUpperCase()}
                    </AvatarFallback>
                  </Avatar>
                  
                  {/* Hidden file input for photo upload */}
                  <input 
                    type="file" 
                    ref={fileInputRef}
                    className="hidden"
                    accept="image/*"
                    onChange={handlePhotoChange}
                  />
                  
                  {/* Camera icon overlay */}
                  <div 
                    className="absolute bottom-0 right-0 bg-primary text-white p-1 rounded-full cursor-pointer hover:bg-primary/90 transition-colors"
                    onClick={handlePhotoClick}
                  >
                    <Camera size={16} />
                  </div>
                </div>
                <div>
                  <p className="text-xl">{user?.name}</p>
                  <p className="text-muted-foreground">{user?.email}</p>
                  <Button 
                    variant="ghost" 
                    size="sm" 
                    className="mt-1 p-0 h-auto text-xs flex items-center gap-1 hover:bg-transparent"
                    onClick={handlePhotoClick}
                    disabled={isUploading}
                  >
                    <Upload size={14} />
                    {isUploading ? "Uploading..." : "Upload photo"}
                  </Button>
                </div>
              </CardTitle>
            </CardHeader>
            <CardContent>
              <Form {...form}>
                <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
                  <FormField
                    control={form.control}
                    name="name"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Full Name</FormLabel>
                        <FormControl>
                          <Input {...field} />
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
                          <Input {...field} disabled />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  
                  <FormField
                    control={form.control}
                    name="phone"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Phone Number</FormLabel>
                        <FormControl>
                          <Input {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  
                  <FormField
                    control={form.control}
                    name="bio"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Bio</FormLabel>
                        <FormControl>
                          <Textarea 
                            placeholder="Tell us about yourself"
                            className="resize-none h-24"
                            {...field} 
                          />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  
                  <FormField
                    control={form.control}
                    name="education"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Education</FormLabel>
                        <FormControl>
                          <Textarea 
                            placeholder="Your educational background"
                            className="resize-none h-24"
                            {...field} 
                          />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  
                  <FormField
                    control={form.control}
                    name="experience"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Work Experience</FormLabel>
                        <FormControl>
                          <Textarea 
                            placeholder="Your work experience"
                            className="resize-none h-24" 
                            {...field} 
                          />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  
                  <FormField
                    control={form.control}
                    name="skills"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Skills (comma-separated)</FormLabel>
                        <FormControl>
                          <Input 
                            placeholder="e.g. JavaScript, React, Node.js"
                            {...field} 
                          />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  
                  <div className="flex justify-end gap-4">
                    <Button 
                      type="button" 
                      variant="outline" 
                      onClick={() => navigate('/student/dashboard')}
                    >
                      Cancel
                    </Button>
                    <Button type="submit" disabled={isLoading}>
                      {isLoading ? 'Saving...' : 'Save Changes'}
                    </Button>
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

export default StudentProfile;
