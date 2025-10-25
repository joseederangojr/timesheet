import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

export default function Home() {
    return (
        <div className="flex min-h-screen items-center justify-center bg-background p-4">
            <Card className="w-full max-w-md">
                <CardHeader className="text-center">
                    <CardTitle className="text-2xl font-bold">
                        Hello World
                    </CardTitle>
                    <CardDescription>
                        Welcome to your new Laravel + Inertia + React
                        application
                    </CardDescription>
                </CardHeader>
                <CardContent className="text-center">
                    <p className="mb-4 text-muted-foreground">
                        This is a simple page with shadcn/ui components working
                        properly.
                    </p>
                    <Button onClick={() => alert('Hello from shadcn/ui!')}>
                        Click me
                    </Button>
                </CardContent>
            </Card>
        </div>
    );
}
