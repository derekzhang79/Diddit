//
//  DIChore.h
//  DidIt
//
//  Created by Matthew Holcombe on 12.12.11.
//  Copyright (c) 2011 Sparkle Mountain. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface DIChore : NSObject

+(DIChore *)choreWithDictionary:(NSDictionary *)dictionary;
-(NSString *)price;
-(NSString *)disp_points;
-(NSString *)disp_expires;

@property (nonatomic, retain) NSDictionary *dictionary;
@property (nonatomic) int chore_id;
@property (nonatomic, retain) NSString *title;
@property (nonatomic, retain) NSString *info;
@property (nonatomic, retain) NSString *itunes_id;
@property (nonatomic) float cost;
@property (nonatomic) int points;
@property (nonatomic) int type_id;
@property (nonatomic) int iap_id;
@property (nonatomic, retain) NSString *icoPath;
@property (nonatomic, retain) NSString *imgPath;
@property (nonatomic) BOOL isFinished;
@property (nonatomic, retain) NSDate *expires;
@property (nonatomic, retain) NSString *subIDs;
@property (nonatomic) int status_id;
@property (nonatomic, retain) NSMutableDictionary *messages;

@end
