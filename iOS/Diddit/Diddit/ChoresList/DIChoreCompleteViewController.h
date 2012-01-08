//
//  DIChoreCompleteViewController.h
//  Diddit
//
//  Created by Matthew Holcombe on 01.07.12.
//  Copyright (c) 2012 Sparkle Mountain. All rights reserved.
//

#import <UIKit/UIKit.h>

#import "ASIFormDataRequest.h"
#import "DIChore.h"

@interface DIChoreCompleteViewController : UIViewController <ASIHTTPRequestDelegate> {
	DIChore *_chore;
}

-(id)initWithChore:(DIChore *)chore;
@end