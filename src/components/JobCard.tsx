
import React from 'react';
import { Link } from 'react-router-dom';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import { Briefcase, MapPin, Calendar } from 'lucide-react';

interface JobCardProps {
  id: string;
  title: string;
  company: string;
  location: string;
  type: 'Internship' | 'Full-time' | 'Part-time';
  deadline: string;
  logo?: string;
}

const JobCard = ({ id, title, company, location, type, deadline, logo }: JobCardProps) => {
  return (
    <Card className="listing-hover overflow-hidden">
      <CardContent className="p-6">
        <div className="flex items-start gap-4">
          <div className="h-12 w-12 rounded bg-recruit-100 flex items-center justify-center overflow-hidden shrink-0">
            {logo ? (
              <img src={logo} alt={`${company} logo`} className="h-full w-full object-cover" />
            ) : (
              <Briefcase className="h-6 w-6 text-recruit-500" />
            )}
          </div>
          <div className="space-y-1">
            <h3 className="font-semibold text-lg leading-none">
              <Link to={`/jobs/${id}`} className="hover:text-primary">
                {title}
              </Link>
            </h3>
            <p className="text-sm text-muted-foreground">{company}</p>
            <div className="flex flex-wrap gap-2 pt-2">
              <Badge variant="outline" className="flex items-center gap-1">
                <MapPin className="h-3 w-3" />
                {location}
              </Badge>
              <Badge 
                variant={type === 'Internship' ? 'secondary' : 'default'}
                className="flex items-center gap-1"
              >
                <Briefcase className="h-3 w-3" />
                {type}
              </Badge>
            </div>
          </div>
        </div>
      </CardContent>
      <CardFooter className="px-6 py-3 border-t bg-muted/50 flex justify-between items-center">
        <div className="flex items-center text-xs text-muted-foreground">
          <Calendar className="mr-1 h-3 w-3" />
          Apply by: {deadline}
        </div>
        <Link 
          to={`/jobs/${id}`}
          className="text-xs font-medium text-primary hover:underline"
        >
          View Details
        </Link>
      </CardFooter>
    </Card>
  );
};

export default JobCard;
