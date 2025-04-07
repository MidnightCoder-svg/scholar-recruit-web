
import React from 'react';
import { Link } from 'react-router-dom';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Briefcase, MapPin, Calendar } from 'lucide-react';

interface JobCardProps {
  id: string;
  title: string;
  company: string;
  location: string;
  type: 'Full-time' | 'Part-time' | 'Internship' | 'Contract';
  deadline: string;
}

const JobCard = ({ id, title, company, location, type, deadline }: JobCardProps) => {
  return (
    <Card className="overflow-hidden hover:shadow-md transition-shadow">
      <CardContent className="p-6">
        <div className="mb-2 flex justify-between items-start">
          <h3 className="font-semibold text-lg line-clamp-1">{title}</h3>
          <Badge 
            variant={type === 'Internship' ? 'secondary' : 'default'}
            className="ml-2 shrink-0"
          >
            {type}
          </Badge>
        </div>
        
        <p className="text-muted-foreground mb-4">{company}</p>
        
        <div className="space-y-2 text-sm text-muted-foreground">
          <div className="flex items-center">
            <MapPin className="h-4 w-4 mr-2 shrink-0" />
            <span>{location}</span>
          </div>
          
          <div className="flex items-center">
            <Calendar className="h-4 w-4 mr-2 shrink-0" />
            <span>Apply by: {deadline}</span>
          </div>
        </div>
      </CardContent>
      
      <CardFooter className="px-6 py-4 bg-gray-50 border-t">
        <Button variant="outline" className="w-full" asChild>
          <Link to={`/jobs/${id}`}>
            View Details
          </Link>
        </Button>
      </CardFooter>
    </Card>
  );
};

export default JobCard;
