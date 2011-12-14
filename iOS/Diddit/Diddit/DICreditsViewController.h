//
//  DICreditsViewController.h
//  Diddit
//
//  Created by Matthew Holcombe on 12.13.11.
//  Copyright (c) 2011 Sparkle Mountain. All rights reserved.
//

#import <UIKit/UIKit.h>

#import "ASIHTTPRequest.h"

@interface DICreditsViewController : UIViewController <UITableViewDelegate, UITableViewDataSource, ASIHTTPRequestDelegate> {
	
	UITableView *_creditsTableView;
	NSArray *_sectionTitles;
	
	NSMutableArray *_chores;
	
	UILabel *_creditsLabel;
}

-(id)initWithChores:(NSMutableArray *)chores;
@end